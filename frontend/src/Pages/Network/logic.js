import { useEffect, useMemo, useRef, useState } from "react";
import { createSocket } from "../../Services/socketService";
import { fetchPeople } from "../../Services/peopleService";
import {
  fetchChatHistory as apiFetchChatHistory,
  sendMessage as apiSendMessage,
} from "../../Services/chatService";
import {
  transcribeWithFallback,
  DEFAULT_DICTATION_LANG,
} from "../../Services/transcriptionService";
import { parseDate, formatTime as fmtTime } from "../../Services/datetime";
import { readMe } from "../../Services/userService";

const AUDIO_MIME_CANDIDATES = [
  "audio/webm;codecs=opus",
  "audio/webm",
  "audio/ogg;codecs=opus",
  "audio/ogg",
  "audio/wav",
];

export default function useNetwork() {
  const [people, setPeople] = useState([]);
  const [selectedPerson, setSelectedPerson] = useState(null);
  const [chatHistory, setChatHistory] = useState({});
  const [currentMessage, setCurrentMessage] = useState("");
  const [searchTerm, setSearchTerm] = useState("");
  const [loadingList, setLoadingList] = useState(false);
  const [loadingChats, setLoadingChats] = useState(false);
  const [error, setError] = useState("");

  const [recording, setRecording] = useState(false);
  const mediaStreamRef = useRef(null);
  const mediaRecorderRef = useRef(null);
  const chunksRef = useRef([]);
  const [transcribing, setTranscribing] = useState(false);
  const [micError, setMicError] = useState("");
  const [dictationLang, setDictationLang] = useState(DEFAULT_DICTATION_LANG);

  const [me] = useState(readMe());
  const iAmMentor = (me?.role || "").toLowerCase() === "mentor";
  const myId = me?.id ?? null;

  const socketSendRef = useRef(null);

  const pickSupportedMime = () => {
    for (const t of AUDIO_MIME_CANDIDATES) {
      if (MediaRecorder.isTypeSupported?.(t)) return t;
    }
    return "";
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
      const { text, notice } = await transcribeWithFallback(blob, dictationLang);
      if (text) {
        setCurrentMessage((prev) => (prev ? (prev + " " + text).trim() : text));
      } else {
        setMicError("No text recognized.");
      }
      if (notice) setMicError(notice);
    } catch (e) {
      setMicError(e?.message || "Transcription error.");
    } finally {
      setTranscribing(false);
    }
  };

  useEffect(() => {
    if (!myId) {
      console.warn("No myId found in localStorage; not joining any room");
      return;
    }

    const { send, dispose } = createSocket({
      myId,
      onMessage: (peerId, msg) => {
        setChatHistory((prev) => ({
          ...prev,
          [peerId]: [...(prev[peerId] || []), msg],
        }));
      },
    });

    socketSendRef.current = send;

    return () => {
      socketSendRef.current = null;
      dispose?.();
    };
  }, [myId]);

  // People list
  useEffect(() => {
    let cancelled = false;
    const controller =
      typeof AbortController !== "undefined" ? new AbortController() : null;

    async function run() {
      setLoadingList(true);
      setError("");
      try {
        const list = await fetchPeople({
          iAmMentor,
          searchTerm,
          myId,
          signal: controller?.signal,
        });
        if (!cancelled) setPeople(list);
      } catch (e) {
        if (!cancelled) {
          setError(e?.message || "Error loading list");
          setPeople([]);
        }
      } finally {
        if (!cancelled) setLoadingList(false);
      }
    }

    const t = setTimeout(run, 150);
    return () => {
      cancelled = true;
      controller?.abort?.();
      clearTimeout(t);
    };
  }, [searchTerm, iAmMentor, myId]);

  const fetchChatHistory = async (person) => {
    const userId = person?.id;
    if (!userId) return;
    if (chatHistory[`${userId}::__loaded`]) return;

    setLoadingChats(true);
    setError("");

    try {
      const messages = await apiFetchChatHistory({ iAmMentor, userId });
      setChatHistory((prev) => ({
        ...prev,
        [userId]: messages,
        [`${userId}::__loaded`]: true,
      }));
    } catch (e) {
      setError(e?.message || "Error loading chat");
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
      const created = await apiSendMessage({
        iAmMentor,
        toId: selectedPerson.id,
        text,
        myRole: iAmMentor ? "mentor" : "user",
        myId,
      });

      try {
        const wirePayload = {
          ...created.raw,
          id: created.id,
          message: created.message,
          sender_id: created.sender_id ?? myId ?? null,
          recipient_id: selectedPerson?.id ?? null,
          timestamp: created.timestamp,
          sender_role:
            created.raw?.sender_role || (iAmMentor ? "mentor" : "user"),
        };
        socketSendRef.current?.(selectedPerson.id, wirePayload);
      } catch (e) {
        console.warn("socket emit failed", e);
      }

      setChatHistory((prev) => ({
        ...prev,
        [selectedPerson.id]: [...(prev[selectedPerson.id] || []), created],
      }));
    } catch (e) {
      setCurrentMessage(text);
      setError(e?.message || "Error sending message");
    }
  };

  const filteredPeople = useMemo(() => people, [people]);

  const formatTime = (dateLike) => {
    const d = parseDate(dateLike) || new Date();
    return fmtTime(d);
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
    recording,
    transcribing,
    micError,
    dictationLang,
    setDictationLang,
    toggleRecording,
    setSearchTerm,
    setCurrentMessage,
    handlePersonSelect,
    handleSendMessage,
    filteredPeople,
    formatTime,
  };
}
