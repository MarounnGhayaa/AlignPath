import { useSelector, useDispatch } from "react-redux";
import API from "../../Services/axios";
import {
  setInput,
  setLoading,
  addMessage,
  setMessages,
} from "../../Features/AiChat/AiChatSlice";

export const useAiChat = () => {
  const dispatch = useDispatch();
  const { input, loading, messages } = useSelector((state) => state.aiChat);

  const token = localStorage.getItem("token");

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
          system: "You are a concise, helpful assistant for our website.",
          messages: nextMessages.map((m) => ({
            role: m.role,
            content: m.content,
          })),
          temperature: 0.7,
          maxOutputTokens: 1024,
        },
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      );

      const data = res.data;

      if (data?.reply) {
        dispatch(addMessage({ role: "model", content: data.reply }));
      } else if (data?.blocked) {
        dispatch(
          addMessage({ role: "model", content: "⚠️ Blocked by safety filters." })
        );
      } else {
        dispatch(
          addMessage({ role: "model", content: "⚠️ No response from AI." })
        );
      }
    } catch (err) {
      dispatch(
        addMessage({ role: "model", content: "⚠️ Network error. Try again." })
      );
    } finally {
      dispatch(setLoading(false));
    }
  };

  const handleFieldChange = (value) => {
    dispatch(setInput(value));
  };

  return {
    input,
    loading,
    messages,
    handleFieldChange,
    sendMessage,
  };
};
