import express from "express";
import http from "http";
import { Server } from "socket.io";

const app = express();
app.use(express.json());

const server = http.createServer(app);
const io = new Server(server, { cors: { origin: "*" } });

const WEBHOOK_SECRET = process.env.WEBHOOK_SECRET || "dev_secret";

io.on("connection", (socket) => {
  socket.on("join", ({ userId }) => {
    if (!userId) return;
    socket.join(`user:${userId}`);
  });
});

app.post("/hooks/message-created", (req, res) => {
  if (req.header("X-Webhook-Secret") !== WEBHOOK_SECRET) return res.sendStatus(403);

  const { recipientIds, payload } = req.body || {};
  if (Array.isArray(recipientIds)) {
    recipientIds.forEach((uid) => io.to(`user:${uid}`).emit("message.created", payload));
  }
  return res.sendStatus(204);
});

const port = process.env.PORT || 4000;
server.listen(port, () => console.log(`Socket server on :${port}`));
