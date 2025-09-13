import { useEffect, useMemo, useRef, useState } from "react";
import API from "../../Services/axios";
import { io } from "socket.io-client";
const SOCKET_URL =
  (typeof import.meta !== "undefined" && import.meta.env && import.meta.env.VITE_SOCKET_URL) ||
  process.env.REACT_APP_SOCKET_URL ||
  "http://127.0.0.1:4000";

const AUDIO_MIME_CANDIDATES = [
  "audio/webm;codecs=opus",
  "audio/webm",
  "audio/ogg;codecs=opus",
  "audio/ogg",
  "audio/wav",
];

const COMMON_STT_LANGS = ["en-US", "ar-SA", "fr-FR", "en-GB"];

const DEFAULT_DICTATION_LANG =
  (typeof navigator !== "undefined" && navigator.language && COMMON_STT_LANGS.includes(navigator.language)
    ? navigator.language
    : "en-US");

const parseDate = (v) => {
  if (!v) return null;
  const d = new Date(v);
  return isNaN(d.getTime()) ? null : d;
};

const readMe = () => {
  try {
    const raw = localStorage.getItem("user") || localStorage.getItem("currentUser");
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
};

const listEndpoints = (iAmMentor) => (iAmMentor ? ["user/users"] : ["user/mentors"]);
const getChatEndpoint = (iAmMentor, id) =>
  iAmMentor ? `user/users/${id}/chats` : `user/mentors/${id}/chats`;
const sendMessageEndpoint = (iAmMentor, id) =>
  iAmMentor ? `user/users/${id}/messages` : `user/mentors/${id}/messages`;

// ---------- Browser SpeechRecognition fallback (hardened & verbose) ----------
/**
 * One-shot dictation using Web Speech API with better resilience & diagnostics.
 * - Works only on secure contexts (https or localhost) and Chromium browsers.
 * - Pass 1: continuous+interim (up to maxWaitMs, default ~15s).
 * - Pass 2/3: compact tries with alternate languages (en-US, ar-SA, etc.).
 * - Returns transcript string (may be ""), throws only on hard errors (perm, support).
 * - Set debug=true to see detailed console logs for event flow.
 */
const browserTranscribeOnce = async ({
  lang = DEFAULT_DICTATION_LANG,
  maxWaitMs = 15000,
  debug = true,
  compactRetryLangs = COMMON_STT_LANGS,
  preflightMic = true,
} = {}) => {
  const SR =
    typeof window !== "undefined" &&
    (window.SpeechRecognition || window.webkitSpeechRecognition);
  if (!SR) throw new Error("Browser speech recognition not supported.");

  const isSecure =
    (typeof window !== "undefined" && window.isSecureContext) ||
    ["localhost", "127.0.0.1"].includes(window.location.hostname);
  if (!isSecure) throw new Error("Speech recognition requires HTTPS or localhost.");

  // Ensure a common, supported lang (avoid rare region tags like ar-LB)
  const primaryLang = COMMON_STT_LANGS.includes(lang) ? lang : "en-US";

  // Optional: preflight mic permission to surface clear errors
  if (preflightMic && navigator.mediaDevices?.getUserMedia) {
    try {
      const s = await navigator.mediaDevices.getUserMedia({ audio: true });
      s.getTracks().forEach((t) => t.stop());
    } catch {
      throw new Error("Microphone permission denied.");
    }
  }

  const runPass = (opts) =>
    new Promise((resolve, reject) => {
      const r = new SR();
      r.lang = opts.lang;
      r.continuous = opts.continuous;
      r.interimResults = opts.interimResults;

      let finalText = "";
      let lastInterim = "";
      let hadResult = false;
      let heardAudio = false;
      let silenceTimer = null;

      const log = (...args) => debug && console.log("[STT]", ...args);

      const stopSoon = () => {
        try {
          r.stop();
        } catch {}
      };

      const resetSilence = () => {
        if (silenceTimer) clearTimeout(silenceTimer);
        // stop ~1.5s after last activity
        silenceTimer = setTimeout(stopSoon, 1500);
      };

      const overallTimer = setTimeout(stopSoon, Math.max(4000, opts.maxWaitMs));

      r.onstart = () => log("start", { lang: opts.lang, cont: opts.continuous, interim: opts.interimResults });
      r.onaudiostart = () => {
        heardAudio = true;
        log("audio start");
        resetSilence();
      };
      r.onsoundstart = () => {
        heardAudio = true;
        log("sound start");
        resetSilence();
      };
      r.onspeechstart = () => {
        heardAudio = true;
        log("speech start");
        resetSilence();
      };
      r.onresult = (e) => {
        resetSilence();
        hadResult = true;
        for (let i = e.resultIndex; i < e.results.length; i++) {
          const res = e.results[i];
          const text = res[0]?.transcript || "";
          if (!res.isFinal && opts.interimResults && text) {
            lastInterim = text;
            log("interim:", lastInterim);
          }
          if (res.isFinal && text) {
            finalText += (finalText ? " " : "") + text;
            lastInterim = "";
            log("final chunk:", text);
          }
        }
      };
      r.onerror = (e) => {
        clearTimeout(overallTimer);
        if (silenceTimer) clearTimeout(silenceTimer);
        log("error:", e?.error || e);
        reject(new Error(e?.error || "Speech recognition error"));
      };
      r.onsoundend = () => {
        log("sound end");
        resetSilence();
      };
      r.onspeechend = () => {
        log("speech end → stopping soon");
        stopSoon();
      };
      r.onaudioend = () => log("audio end");
      r.onend = () => {
        clearTimeout(overallTimer);
        if (silenceTimer) clearTimeout(silenceTimer);
        // if no finals, fall back to last interim (better than empty)
        const out = (finalText || lastInterim).trim();
        log("end", { heardAudio, hadResult, out });
        resolve(out);
      };

      try {
        r.start();
      } catch (err) {
        clearTimeout(overallTimer);
        if (silenceTimer) clearTimeout(silenceTimer);
        reject(err);
      }
    });

  // Pass 1: generous mode (continuous + interim)
  const pass1 = await runPass({
    lang: primaryLang,
    continuous: true,
    interimResults: true,
    maxWaitMs,
  });
  if (pass1) return pass1;

  // Pass 2+: compact tries with alternates (skip duplicates)
  const candidates = [primaryLang, ...compactRetryLangs].filter(
    (x, i, arr) => arr.indexOf(x) === i
  );
  for (const l of candidates) {
    if (l === primaryLang) continue;
    const pass = await runPass({
      lang: l,
      continuous: false,
      interimResults: false,
      maxWaitMs: Math.min(9000, maxWaitMs),
    });
    if (pass) return pass;
  }

  // Nothing recognized
  return "";
};

export default function useNetwork() {
  const [people, setPeople] = useState([]);
  const [selectedPerson, setSelectedPerson] = useState(null);
  const [chatHistory, setChatHistory] = useState({});
  const [currentMessage, setCurrentMessage] = useState("");
  const [searchTerm, setSearchTerm] = useState("");
  const [loadingList, setLoadingList] = useState(false);
  const [loadingChats, setLoadingChats] = useState(false);
  const [error, setError] = useState("");

  // Voice-to-text state
  const [recording, setRecording] = useState(false);
  const mediaStreamRef = useRef(null);
  const mediaRecorderRef = useRef(null);
  const chunksRef = useRef([]);
  const [transcribing, setTranscribing] = useState(false);
  const [micError, setMicError] = useState("");
  const [dictationLang, setDictationLang] = useState(DEFAULT_DICTATION_LANG);

  // Auth/user
  const [me] = useState(readMe());
  const iAmMentor = (me?.role || "").toLowerCase() === "mentor";
  const myId = me?.id ?? null;

  // SOCKET.IO
  const socketRef = useRef(null);

  // ---- Voice helpers ----
  const pickSupportedMime = () => {
    for (const t of AUDIO_MIME_CANDIDATES) {
      if (MediaRecorder.isTypeSupported?.(t)) return t;
    }
    return ""; // let browser pick default
  };

  const startRecording = async () => {
    setMicError("");
    if (!navigator.mediaDevices?.getUserMedia) {
      setMicError("Microphone not supported in this browser.");
      return;
    }
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      mediaStreamRef.current = stream;

      const mimeType = pickSupportedMime();
      const rec = new MediaRecorder(stream, mimeType ? { mimeType } : undefined);
      chunksRef.current = [];

      rec.ondataavailable = (e) => {
        if (e.data && e.data.size > 0) chunksRef.current.push(e.data);
      };
      rec.onstop = async () => {
        const mime = rec.mimeType || "audio/webm";
        const blob = new Blob(chunksRef.current, { type: mime });
        await transcribeAudio(blob);
        // cleanup
        stream.getTracks().forEach((t) => t.stop());
        mediaStreamRef.current = null;
        mediaRecorderRef.current = null;
        chunksRef.current = [];
      };

      mediaRecorderRef.current = rec;
      rec.start();
      setRecording(true);
    } catch (err) {
      setMicError(err?.message || "Could not access microphone.");
    }
  };

  const stopRecording = () => {
    const rec = mediaRecorderRef.current;
    if (rec && rec.state !== "inactive") rec.stop();
    setRecording(false);
  };

  const toggleRecording = () => {
    if (recording) stopRecording();
    else startRecording();
  };

  const transcribeAudio = async (blob) => {
    setTranscribing(true);
    setMicError("");
    try {
      // inside transcribeAudio in logic.js
const form = new FormData();
form.append(
  "audio",
  blob,
  `clip.${blob.type.includes("ogg") ? "ogg" : blob.type.includes("wav") ? "wav" : "webm"}`
);
// NEW: pass the language the user selected (or default)
form.append("language", (dictationLang || "en-US").split("-")[0]);


      const token = localStorage.getItem("token");
      const res = await API.post("user/transcribe", form, {
        headers: token ? { Authorization: `Bearer ${token}` } : undefined,
      });

      const text = (res?.data?.text || "").trim();
      if (text) {
        setCurrentMessage((prev) => (prev ? (prev + " " + text).trim() : text));
      } else {
        setMicError("No text recognized.");
      }
    } catch (e) {
      const status = e?.response?.status;
      const server = e?.response?.data;
      const retryAfter =
        parseInt(e?.response?.headers?.["retry-after"] || "0", 10) || 0;

      const serverMsg =
        server?.error?.error?.message ||
        server?.error?.message ||
        server?.message ||
        "";

      if (status === 429 && server?.source === "openai") {
        // Quota exceeded — robust browser dictation with diagnostics
        try {
          setMicError(
            `OpenAI quota exceeded. Switching to browser speech-to-text… ${
              retryAfter ? `Wait ${retryAfter}s if needed.` : ""
            }`
          );

          // First try the selected language (normalized to a common one)
          const primary = COMMON_STT_LANGS.includes(dictationLang)
            ? dictationLang
            : "en-US";

          let localText = await browserTranscribeOnce({
            lang: primary,
            maxWaitMs: 15000,
            debug: true,
          });

          // If empty, try a couple of alternates (e.g., Arabic if you’re likely using it)
          if (!localText) {
            const alt = primary.startsWith("ar") ? "en-US" : "ar-SA";
            localText = await browserTranscribeOnce({
              lang: alt,
              maxWaitMs: 12000,
              debug: true,
            });
          }

          if (localText) {
            setCurrentMessage((prev) =>
              prev ? (prev + " " + localText).trim() : localText
            );
            setMicError("Used browser speech-to-text due to OpenAI quota.");
          } else {
            setMicError(
              "No speech detected with browser fallback. Make sure you’re on Chrome (or Edge), mic is allowed, and you’re on HTTPS or localhost."
            );
          }
        } catch (err) {
          setMicError(
            `OpenAI quota exceeded and browser STT failed: ${
              err?.message || "unknown error"
            }`
          );
        }
      } else if (status === 429) {
        setMicError(
          `You’re sending audio too fast${
            retryAfter ? ` — wait ${retryAfter}s` : ""
          }. ${serverMsg}`
        );
      } else {
        setMicError(
          status
            ? `Transcription failed (${status}). ${serverMsg}`
            : e?.message || "Transcription error."
        );
      }
    } finally {
      setTranscribing(false);
    }
  };

  // ---- SOCKET.IO: connect once per myId ----
  useEffect(() => {
    if (!myId) {
      console.warn("No myId found in localStorage; not joining any room");
      return;
    }

    const socket = io(SOCKET_URL, { transports: ["websocket"], autoConnect: true });
    socketRef.current = socket;

    const joinMyRoom = () => {
      console.log("socket connected", socket.id, "→ joining user:", myId);
      socket.emit("join", { userId: myId });
    };

    socket.on("connect", joinMyRoom);
    socket.io.on("reconnect", joinMyRoom);
    socket.on("joined", ({ room }) => console.log("joined ack:", room));

    socket.on("message.created", (data) => {
      const msg = {
        id: data?.id,
        message: data?.message ?? data?.body ?? "",
        sender_id: data?.sender_id ?? null,
        isFromMentor:
          typeof data?.isFromMentor === "boolean"
            ? data.isFromMentor
            : (data?.sender_role || "").toLowerCase() === "mentor",
        timestamp: parseDate(data?.timestamp) || new Date(),
      };

      const peerId =
        data?.peer_id ?? (msg.sender_id === myId ? undefined : msg.sender_id);

      if (!peerId) return;

      setChatHistory((prev) => ({
        ...prev,
        [peerId]: [...(prev[peerId] || []), msg],
      }));
    });

    socket.on("connect_error", (e) =>
      console.warn("socket connect error", e?.message || e)
    );

    return () => {
      try {
        socket.off("message.created");
        socket.off("joined");
        socket.off("connect", joinMyRoom);
        socket.io?.off?.("reconnect", joinMyRoom);
        socket.disconnect();
      } catch {}
      socketRef.current = null;
    };
  }, [myId]);

  // ---- PEOPLE LIST ----
  useEffect(() => {
    let cancelled = false;
    const controller =
      typeof AbortController !== "undefined" ? new AbortController() : null;

    async function fetchList() {
      setLoadingList(true);
      setError("");
      const candidates = listEndpoints(iAmMentor);

      let data = [];
      let lastErr = null;

      for (const url of candidates) {
        try {
          const token = localStorage.getItem("token");
          const res = await API.get(url, {
            params: searchTerm ? { search: searchTerm } : undefined,
            headers: token ? { Authorization: `Bearer ${token}` } : undefined,
            signal: controller?.signal,
          });
          data = res?.data?.data ?? res?.data ?? [];
          if (Array.isArray(data)) break;
        } catch (e) {
          lastErr = e;
        }
      }

      if (!Array.isArray(data)) {
        if (!cancelled) {
          const msg =
            lastErr?.response?.status
              ? `List fetch failed (${lastErr.response.status})`
              : lastErr?.message || "Error loading list";
          setError(msg);
          setPeople([]);
        }
        setLoadingList(false);
        return;
      }

      const normalized = data
        .map((u) => ({
          ...u,
          role: (u.role || (iAmMentor ? "user" : "mentor")).toLowerCase(),
          expertise: Array.isArray(u.expertise) ? u.expertise : [],
        }))
        .filter((u) => (myId ? u.id !== myId : true));

      if (!cancelled) setPeople(normalized);
      if (!cancelled) setLoadingList(false);
    }

    const t = setTimeout(fetchList, 150);
    return () => {
      cancelled = true;
      controller?.abort?.();
      clearTimeout(t);
    };
  }, [searchTerm, iAmMentor, myId]);

  // ---- CHATS ----
  const fetchChatHistory = async (person) => {
    const userId = person?.id;
    if (!userId) return;
    if (chatHistory[`${userId}::__loaded`]) return;

    setLoadingChats(true);
    setError("");

    try {
      const token = localStorage.getItem("token");
      const res = await API.get(getChatEndpoint(iAmMentor, userId), {
        headers: token ? { Authorization: `Bearer ${token}` } : undefined,
      });

      const rows = Array.isArray(res?.data?.data)
        ? res.data.data
        : Array.isArray(res?.data)
        ? res.data
        : [];
      const messages = rows.map((m) => {
        const ts = parseDate(m.timestamp || m.created_at) || new Date();
        const text = m.message ?? m.body ?? "";
        const fromMentor =
          typeof m.isFromMentor === "boolean"
            ? m.isFromMentor
            : (m.sender_role || "").toLowerCase() === "mentor";
        return {
          id: m.id,
          message: text,
          isFromMentor: fromMentor,
          timestamp: ts,
          sender_id: m.sender_id ?? null,
        };
      });

      setChatHistory((prev) => ({
        ...prev,
        [userId]: messages,
        [`${userId}::__loaded`]: true,
      }));
    } catch (e) {
      const msg =
        e?.response?.status
          ? `Failed to load chat (${e.response.status})`
          : e?.message || "Error loading chat";
      setError(msg);
    } finally {
      setLoadingChats(false);
    }
  };

  const handlePersonSelect = (person) => {
    setSelectedPerson(person);
    fetchChatHistory(person);
  };

  const handleSendMessage = async () => {
    if (!currentMessage.trim() || !selectedPerson) return;
    const text = currentMessage.trim();
    setCurrentMessage("");

    try {
      const token = localStorage.getItem("token");
      const res = await API.post(
        sendMessageEndpoint(iAmMentor, selectedPerson.id),
        { message: text },
        { headers: token ? { Authorization: `Bearer ${token}` } : undefined }
      );

      const createdRaw = res?.data?.data ?? res?.data ?? {};
      const created = {
        id: createdRaw.id,
        message: createdRaw.message ?? createdRaw.body ?? text,
        timestamp: parseDate(createdRaw.timestamp || createdRaw.created_at) || new Date(),
        isFromMentor:
          typeof createdRaw.isFromMentor === "boolean"
            ? createdRaw.isFromMentor
            : (createdRaw.sender_role || (iAmMentor ? "mentor" : "user")).toLowerCase() === "mentor",
        sender_id: createdRaw.sender_id ?? myId ?? null,
      };

      setChatHistory((prev) => ({
        ...prev,
        [selectedPerson.id]: [...(prev[selectedPerson.id] || []), created],
      }));
    } catch (e) {
      setCurrentMessage(text);
      const msg =
        e?.response?.status
          ? `Failed to send message (${e.response.status})`
          : e?.message || "Error sending message";
      setError(msg);
    }
  };

  const filteredPeople = useMemo(() => people, [people]);

  const formatTime = (dateLike) => {
    const d = parseDate(dateLike) || new Date();
    return new Intl.DateTimeFormat(undefined, { hour: "2-digit", minute: "2-digit" }).format(d);
  };

  return {
    people,
    selectedPerson,
    chatHistory,
    currentMessage,
    searchTerm,
    loadingList,
    loadingChats,
    error,
    iAmMentor,
    myId,
    // voice-to-text
    recording,
    transcribing,
    micError,
    dictationLang,
    setDictationLang,
    toggleRecording,
    // input/actions
    setSearchTerm,
    setCurrentMessage,
    handlePersonSelect,
    handleSendMessage,
    filteredPeople,
    formatTime,
  };
}
