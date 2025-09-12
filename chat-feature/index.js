import express from "express";
import http from "http";
import { Server } from "socket.io";

const app = express();
app.use(express.json());

const server = http.createServer(app);
const io = new Server(server, { cors: { origin: "*" } });

const WEBHOOK_SECRET = process.env.WEBHOOK_SECRET || "dev_secret";

io.on("connection", (socket) => {
  console.log("socket connected:", socket.id);

  socket.on("join", ({ userId }) => {
    if (!userId) return;
    const room = `user:${userId}`;
    socket.join(room);
    console.log(`socket ${socket.id} joined ${room}`);
    socket.emit("joined", { room });
  });

  socket.on("disconnect", (reason) => {
    console.log("socket disconnected:", socket.id, reason);
  });
});

app.get("/health", (_req, res) => {
  res.json({ ok: true, rooms: [...io.sockets.adapter.rooms.keys()] });
});

app.post("/hooks/message-created", (req, res) => {
  if (req.header("X-Webhook-Secret") !== WEBHOOK_SECRET) return res.sendStatus(403);

  const { recipientIds, payload } = req.body || {};
  console.log("webhook received", { recipients: recipientIds });

  if (Array.isArray(recipientIds)) {
    recipientIds.forEach((uid) => {
      const room = `user:${uid}`;
      io.to(room).emit("message.created", payload);
      console.log("emitted message.created to", room);
    });
  }
  return res.sendStatus(204);
});

const port = process.env.PORT || 4000;
server.listen(port, () => console.log(`Socket server on :${port}`));
