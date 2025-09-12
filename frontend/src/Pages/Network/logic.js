import { useEffect, useMemo, useRef, useState } from "react";
import API from "../../Services/axios";
import { io } from "socket.io-client";

const SOCKET_URL =
  (typeof import.meta !== "undefined" && import.meta.env && import.meta.env.VITE_SOCKET_URL) ||
  process.env.REACT_APP_SOCKET_URL ||
  "http://127.0.0.1:4000";

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

export default function useNetwork() {
  const [people, setPeople] = useState([]);
  const [selectedPerson, setSelectedPerson] = useState(null);
  const [chatHistory, setChatHistory] = useState({});
  const [currentMessage, setCurrentMessage] = useState("");
  const [searchTerm, setSearchTerm] = useState("");
  const [loadingList, setLoadingList] = useState(false);
  const [loadingChats, setLoadingChats] = useState(false);
  const [error, setError] = useState("");

  const [me] = useState(readMe());
  const iAmMentor = (me?.role || "").toLowerCase() === "mentor";
  const myId = me?.id ?? null;


  const socketRef = useRef(null);

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
        data?.peer_id ??
        (msg.sender_id === myId ? undefined : msg.sender_id);

      if (!peerId) return;

      setChatHistory((prev) => ({
        ...prev,
        [peerId]: [...(prev[peerId] || []), msg],
      }));
    });

    socket.on("connect_error", (e) => console.warn("socket connect error", e?.message || e));

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

  useEffect(() => {
    let cancelled = false;
    const controller = typeof AbortController !== "undefined" ? new AbortController() : null;

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

      const rows = Array.isArray(res?.data?.data) ? res.data.data : Array.isArray(res?.data) ? res.data : [];
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
    setSearchTerm,
    setCurrentMessage,
    handlePersonSelect,
    handleSendMessage,
    filteredPeople,
    formatTime,
  };
}
