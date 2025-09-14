import express from "express";
import http from "http";
import { Server } from "socket.io";

const app = express();
app.use(express.json());

const server = http.createServer(app);
const io = new Server(server, { cors: { origin: "*" } });

const WEBHOOK_SECRET = process.env.WEBHOOK_SECRET || "dev_secret";

const normalizeMessageForRecipient = (payload, recipientId) => {
  const senderId =
    payload?.sender_id ??
    payload?.senderId ??
    payload?.from_id ??
    payload?.fromId ??
    null;

  const recipientField =
    payload?.recipient_id ??
    payload?.recipientId ??
    payload?.to_id ??
    payload?.toId ??
    null;

  let peerId = null;
  if (senderId && senderId !== recipientId) peerId = senderId;
  else if (recipientField && recipientField !== recipientId) peerId = recipientField;
  else peerId = senderId || recipientField || null;

  return { ...payload, peer_id: peerId };
};

io.on("connection", (socket) => {
  socket.on("join", ({ userId }) => {
    if (!userId) return;
    const room = `user:${userId}`;
    socket.join(room);
    console.log("socket", socket.id, "joined user:", userId);
    socket.emit("joined", { room });
  });

  socket.on("message.send", ({ to, payload }) => {
    try {
      if (!to || !payload) return;
      const room = `user:${to}`;
      io.to(room).emit("message.created", normalizeMessageForRecipient(payload, to));
      console.log("relayed message.send to", room, "payload.id:", payload?.id);
    } catch (e) {
      console.error("message.send relay error:", e);
    }
  });

  socket.on("disconnect", (reason) => {
    console.log("socket disconnected", socket.id, reason);
  });
});

app.get("/health", (_req, res) => res.send("ok"));

app.post("/hooks/message-created", (req, res) => {
  try {
    const secret = req.header("X-Webhook-Secret");
    if (secret !== WEBHOOK_SECRET) {
      return res.status(401).send("invalid secret");
    }
    const { recipientIds, payload } = req.body || {};
    console.log("webhook received", { recipients: recipientIds });

    if (Array.isArray(recipientIds)) {
      recipientIds.forEach((uid) => {
        const room = `user:${uid}`;
        io.to(room).emit("message.created", normalizeMessageForRecipient(payload, uid));
        console.log("emitted message.created to", room);
      });
    }
    return res.sendStatus(204);
  } catch (e) {
    console.error("webhook error", e);
    return res.sendStatus(500);
  }
});

const port = process.env.PORT || 4000;
server.listen(port, () => console.log(`Socket server on :${port}`));
