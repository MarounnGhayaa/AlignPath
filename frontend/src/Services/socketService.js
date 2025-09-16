import { io } from "socket.io-client";
import { parseDate } from "./datetime";

const SOCKET_URL =
  (typeof import.meta !== "undefined" &&
    import.meta.env &&
    import.meta.env.VITE_SOCKET_URL) ||
  (typeof process !== "undefined" &&
    process.env &&
    process.env.REACT_APP_SOCKET_URL) ||
  "http://127.0.0.1:4000";

export function createSocket({ myId, onMessage, onStatus }) {
  const token = (typeof localStorage !== "undefined" && localStorage.getItem("token")) || "";
  const auth = token ? { authorization: `Bearer ${token}`, token: `Bearer ${token}` } : undefined;
  const socket = io(SOCKET_URL, {
    transports: ["websocket"],
    autoConnect: true,
    auth,
  });

  const joinMyRoom = () => {
    try {
      socket.emit("join", { userId: myId });
      onStatus?.("joined");
    } catch {}
  };

  if (socket.connected) joinMyRoom();
  socket.on("connect", joinMyRoom);
  socket.io?.on?.("reconnect", joinMyRoom);

  socket.on("joined", (data) => onStatus?.("joined:" + (data?.room || "")));

  socket.on("message.created", (data) => {
    const senderId =
      data?.sender_id ?? data?.senderId ?? data?.from_id ?? data?.fromId ?? null;
    const recipientId =
      data?.recipient_id ??
      data?.recipientId ??
      data?.to_id ??
      data?.toId ??
      null;
    const senderRole = (data?.sender_role || data?.senderRole || "").toLowerCase();
    const body = data?.message ?? data?.body ?? data?.text ?? "";
    const when =
      parseDate(data?.timestamp || data?.created_at || data?.createdAt) ||
      new Date();

    const msg = {
      id: data?.id,
      message: body,
      sender_id: senderId,
      isFromMentor:
        typeof data?.isFromMentor === "boolean"
          ? data.isFromMentor
          : senderRole === "mentor",
      timestamp: when,
    };

    let peerId = data?.peer_id ?? data?.peerId ?? null;
    if (!peerId) {
      if (senderId && recipientId) {
        peerId = senderId === myId ? recipientId : senderId;
      } else if (senderId) {
        peerId = senderId === myId ? recipientId || null : senderId;
      } else if (recipientId) {
        peerId = recipientId === myId ? senderId || null : recipientId;
      }
    }
    if (!peerId) return;
    onMessage?.(peerId, msg);
  });

  socket.on("connect_error", (e) =>
    onStatus?.("connect_error:" + (e?.message || ""))
  );
  socket.on("disconnect", (reason) => onStatus?.("disconnect:" + reason));

  const send = (to, payload) => {
    try {
      socket.emit("message.send", { to, payload });
    } catch {}
  };

  const dispose = () => {
    try {
      socket.off("message.created");
      socket.off("joined");
      socket.off("connect", joinMyRoom);
      socket.io?.off?.("reconnect", joinMyRoom);
      socket.disconnect();
    } catch {}
  };

  return { socket, send, dispose };
}

export { SOCKET_URL };
