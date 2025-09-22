import "./style.css";
import AiChat from "../../Pages/AiChat";
import { MessageCircle, X } from "lucide-react";
import { useState } from "react";

const FloatingChatbot = () => {
  const [isChatbotOpen, setIsChatbotOpen] = useState(false);

  return (
    <>
      <button
        className={`floating-chatbot-button ${isChatbotOpen ? "open" : ""}`}
        onClick={() => setIsChatbotOpen(!isChatbotOpen)}
        aria-label="Toggle AI Chatbot"
      >
        {isChatbotOpen ? <X size={24} /> : <MessageCircle size={24} />}
      </button>
      {isChatbotOpen && (
        <div
          className="chatbot-overlay"
          onClick={() => setIsChatbotOpen(false)}
        >
          <div
            className="chatbot-container"
            onClick={(e) => e.stopPropagation()}
          >
            <AiChat />
          </div>
        </div>
      )}
    </>
  );
};

export default FloatingChatbot;
