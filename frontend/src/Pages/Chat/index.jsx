import "./style.css";
import Input from "../../Components/Input";
import Button from "../../Components/Button";
import { SendHorizontal } from "lucide-react";
import { useNavigate } from "react-router-dom";
import FloatingChatbot from "../../Components/FloatingChatbot";
import { io } from "socket.io-client";
import { useEffect, useState } from "react";

const token = (typeof localStorage !== "undefined" && localStorage.getItem("token")) || "";
const socket = io("http://localhost:4000", {
  auth: token ? { authorization: `Bearer ${token}`, token: `Bearer ${token}` } : undefined,
});

const Chat = () => {
  const navigate = useNavigate();
  const [message, setMessage] = useState("");
  const [messageReceived, setMessageReceived] = useState("");
  const sendMessage = () => {
    socket.emit("send_message", { message });
  };

  useEffect(() => {
    socket.on("receive_message", (data) => {
      setMessageReceived(data.message);
    });
  });

  return (
    <div className="chat-body">
      <h1>
        <Button
          insiders={"←"}
          className={"chat-left-button"}
          onClickListener={() => {
            navigate("/network");
          }}
        />{" "}
        &nbsp; Back to Network
      </h1>
      <div className="chat-container">
        <div className="chat-container-header">
          <h3>Mentor Name</h3>
        </div>
        <div className="chat-container-body">
          <h1>{messageReceived}</h1>
        </div>
        <div className="chat-container-footer">
          <div className="chat-container-footer-row">
            <Input
              type={"text"}
              name={"message"}
              hint={"Enter your message..."}
              className={"input-style"}
              onChangeListener={(e) => {
                setMessage(e.target.value);
              }}
            />
            <Button
              insiders={<SendHorizontal />}
              className="primary-button auth-button"
              onClickListener={sendMessage}
            />
          </div>
        </div>
      </div>
      <FloatingChatbot />
    </div>
  );
};

export default Chat;
