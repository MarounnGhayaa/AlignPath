import { useSelector, useDispatch } from "react-redux";
import API from "../../Services/axios";
import {
  setInput,
  setLoading,
  addMessage,
  setMessages,
  clearChat,
} from "../../Features/AiChat/AiChatSlice";
import { useEffect, useRef, useState } from "react";

export const useAiChat = () => {
  const dispatch = useDispatch();
  const { input, loading, messages } = useSelector((state) => state.aiChat);

  const [threadId, setThreadId] = useState(
    () => localStorage.getItem("aiChatThreadId") || null
  );
  const [threads, setThreads] = useState([]);
  const [sidebarOpen, setSidebarOpen] = useState(false);

  const token = localStorage.getItem("token");
  const currentUserId = (() => {
    try {
      const u = JSON.parse(localStorage.getItem("user") || "null");
      return u?.id ? String(u.id) : null;
    } catch (_) {
      return null;
    }
  })();
  const scrollRef = useRef(null);

  const authHeader = { headers: { Authorization: `Bearer ${token}` } };

  const fetchThreads = async () => {
    try {
      const res = await API.get("/user/chat/threads?limit=30", authHeader);
      const items = res.data?.items || [];
      setThreads(items);
      if (
        threadId &&
        !items.some((t) => String(t.id) === String(threadId))
      ) {
        localStorage.removeItem("aiChatThreadId");
        setThreadId(null);
        dispatch(clearChat());
      }
    } catch (e) {
      // no-op
    }
  };

  const openThread = async (id) => {
    try {
      const res = await API.get(`/user/chat/threads/${id}`, authHeader);
      const msgs = (res.data?.messages || []).map(m => ({
        role: m.role,
        content: m.content,
      }));
      setThreadId(String(id));
      localStorage.setItem("aiChatThreadId", String(id));
      dispatch(setMessages(msgs));
      setSidebarOpen(false);
    } catch (e) {
      localStorage.removeItem("aiChatThreadId");
      setThreadId(null);
    }
  };

  useEffect(() => {
    const savedForUser = localStorage.getItem("aiChatUserId");
    if (currentUserId && savedForUser && savedForUser !== String(currentUserId)) {
      localStorage.removeItem("aiChatThreadId");
      dispatch(clearChat());
      setThreadId(null);
    }
    if (currentUserId) {
      localStorage.setItem("aiChatUserId", String(currentUserId));
    }
    fetchThreads();
    if (!threadId) {
      dispatch(clearChat());
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  useEffect(() => {
    if (threadId && messages.length === 0) {
      openThread(threadId);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [threadId]);

  useEffect(() => {
    if (scrollRef.current) {
      scrollRef.current.scrollTop = scrollRef.current.scrollHeight;
    }
  }, [messages, loading]);

  const sendMessage = async (e) => {
    e.preventDefault();
    if (!input.trim() || loading) return;

    const nextMessages = [...messages, { role: "user", content: input.trim() }];
    dispatch(setMessages(nextMessages));
    dispatch(setInput(""));
    dispatch(setLoading(true));

    try {
      const res = await API.post(
        "/user/chat",
        {
          thread_id: threadId ? Number(threadId) : undefined,
          system: "You are a concise, helpful assistant for our website.",
          messages: nextMessages,
          temperature: 0.7,
          maxOutputTokens: 1024,
        },
        authHeader
      );

      const data = res.data;
      if (data?.thread_id && data.thread_id !== threadId) {
        setThreadId(String(data.thread_id));
        localStorage.setItem("aiChatThreadId", String(data.thread_id));
      }

      if (data?.reply) {
        dispatch(addMessage({ role: "model", content: data.reply }));
      } else if (data?.blocked) {
        dispatch(addMessage({ role: "model", content: "⚠️ Blocked by safety filters." }));
      } else {
        dispatch(addMessage({ role: "model", content: "⚠️ No response from AI." }));
      }

      fetchThreads();
    } catch (err) {
      dispatch(addMessage({ role: "model", content: "⚠️ Network error. Try again." }));
    } finally {
      dispatch(setLoading(false));
    }
  };

  const handleFieldChange = (value) => dispatch(setInput(value));

  const newChat = () => {
    localStorage.removeItem("aiChatThreadId");
    setThreadId(null);
    dispatch(clearChat());
  };

  return {
    input, loading, messages,
    threadId, threads, sidebarOpen,
    setSidebarOpen,
    handleFieldChange, sendMessage, newChat, openThread,
    scrollRef,
  };
};
