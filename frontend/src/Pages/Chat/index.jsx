import "./style.css";
import Input from "../../Components/Input";
import Button from "../../Components/Button";
import { SendHorizontal } from "lucide-react";
import { useNavigate } from "react-router-dom";
import FloatingChatbot from "../../Components/FloatingChatbot";

const Chat = () => {
  const navigate = useNavigate();

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
        <div className="chat-container-body"></div>
        <div className="chat-container-footer">
          <div className="chat-container-footer-row">
            <Input
              type={"text"}
              name={"message"}
              hint={"Enter your message..."}
              className={"input-style"}
            />
            <Button
              insiders={<SendHorizontal />}
              className="primary-button auth-button"
            />
          </div>
        </div>
      </div>
      <FloatingChatbot />
    </div>
  );
};

export default Chat;
