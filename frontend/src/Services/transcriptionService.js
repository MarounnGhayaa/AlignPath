import API from "./axios";

const COMMON_STT_LANGS = ["en-US", "ar-SA", "fr-FR", "en-GB"];

const DEFAULT_DICTATION_LANG =
  (typeof navigator !== "undefined" &&
    navigator.language &&
    COMMON_STT_LANGS.includes(navigator.language)
    ? navigator.language
    : "en-US");

export { DEFAULT_DICTATION_LANG };

export async function browserTranscribeOnce({
  lang = DEFAULT_DICTATION_LANG,
  maxWaitMs = 15000,
  debug = true,
  compactRetryLangs = COMMON_STT_LANGS,
  preflightMic = true,
} = {}) {
  const SR =
    typeof window !== "undefined" &&
    (window.SpeechRecognition || window.webkitSpeechRecognition);
  if (!SR) throw new Error("Browser speech recognition not supported.");

  const isSecure =
    (typeof window !== "undefined" && window.isSecureContext) ||
    ["localhost", "127.0.0.1"].includes(window.location.hostname);
  if (!isSecure) throw new Error("Speech recognition requires HTTPS or localhost.");

  const primaryLang = COMMON_STT_LANGS.includes(lang) ? lang : "en-US";

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
        silenceTimer = setTimeout(stopSoon, 1500);
      };

      const overallTimer = setTimeout(stopSoon, Math.max(4000, opts.maxWaitMs));

      r.onstart = () =>
        log("start", {
          lang: opts.lang,
          cont: opts.continuous,
          interim: opts.interimResults,
        });
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

  const pass1 = await runPass({
    lang: primaryLang,
    continuous: true,
    interimResults: true,
    maxWaitMs,
  });
  if (pass1) return pass1;

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
  return "";
}

export async function transcribeAudio(blob, dictationLang) {
  const form = new FormData();
  form.append(
    "audio",
    blob,
    `clip.${
      blob.type.includes("ogg")
        ? "ogg"
        : blob.type.includes("wav")
        ? "wav"
        : "webm"
    }`
  );
  form.append("language", (dictationLang || "en-US").split("-")[0]);

  const token = localStorage.getItem("token");
  const res = await API.post("user/transcribe", form, {
    headers: token ? { Authorization: `Bearer ${token}` } : undefined,
  });

  const text = (res?.data?.text || "").trim();
  return { text };
}

export async function transcribeWithFallback(blob, dictationLang) {
  try {
    const { text } = await transcribeAudio(blob, dictationLang);
    return { text, notice: "" };
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

    if (status === 429) {
      let notice = `Too many requests${
        retryAfter ? ` — wait ${retryAfter}s` : ""
      }. ${serverMsg}`;
      try {
        const localText = await browserTranscribeOnce({
          lang: dictationLang,
          maxWaitMs: 15000,
          debug: true,
        });
        if (localText) {
          return {
            text: localText,
            notice: notice + " | Used browser speech-to-text as a fallback.",
          };
        }
      } catch {}
      throw new Error(notice);
    } else if (status === 503 || status === 500) {
      throw new Error(
        `Transcription service error. ${serverMsg || "Please try again."}`
      );
    } else {
      throw new Error(
        status
          ? `Transcription failed (${status}). ${serverMsg}`
          : e?.message || "Transcription error."
      );
    }
  }
}
