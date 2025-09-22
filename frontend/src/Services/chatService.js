import API from "./axios";
import { parseDate } from "./datetime";

const getChatEndpoint = (iAmMentor, id) =>
  iAmMentor ? `user/users/${id}/chats` : `user/mentors/${id}/chats`;
const sendMessageEndpoint = (iAmMentor, id) =>
  iAmMentor ? `user/users/${id}/messages` : `user/mentors/${id}/messages`;

export async function fetchChatHistory({ iAmMentor, userId }) {
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

  return messages;
}

export async function sendMessage({ iAmMentor, toId, text, myRole, myId }) {
  const token = localStorage.getItem("token");
  const res = await API.post(
    sendMessageEndpoint(iAmMentor, toId),
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
        : (createdRaw.sender_role ||
            myRole ||
            (iAmMentor ? "mentor" : "user")
          ).toLowerCase() === "mentor",
    sender_id: createdRaw.sender_id ?? myId ?? null,
    raw: createdRaw,
  };

  return created;
}
