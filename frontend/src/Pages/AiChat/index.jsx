import "./style.css";
import Button from "../../Components/Button";
import Input from "../../Components/Input";
import { SendHorizontal } from "lucide-react";
import { useAiChat } from "./logic";

const AiChat = () => {
  const { input, loading, messages, handleFieldChange, sendMessage } =
    useAiChat();

  return (
    <div className="ai-chat-container">
      <h2 className="ai-chat-title">Pathfinder</h2>

      <div className="ai-chat-messages">
        {messages.map((m, i) => (
          <div
            key={i}
            className={`ai-chat-message ${
              m.role === "user" ? "ai-chat-user" : "ai-chat-model"
            }`}
          >
            {m.content}
          </div>
        ))}
        {loading && <div className="ai-chat-loading">Thinking…</div>}
      </div>

      <form onSubmit={sendMessage} className="ai-chat-form">
        <div className="ai-chat-sending">
          <Input
            hint={"Ask me anything..."}
            value={input}
            onChangeListener={(e) => handleFieldChange(e.target.value)}
          />
          <Button
            disabled={loading}
            className={"primary-button"}
            text={<SendHorizontal />}
          />
        </div>
      </form>
    </div>
  );
};

export default AiChat;
