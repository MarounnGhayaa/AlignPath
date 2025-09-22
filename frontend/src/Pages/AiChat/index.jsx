import "./style.css";
import Button from "../../Components/Button";
import Input from "../../Components/Input";
import { SendHorizontal, Plus, History } from "lucide-react";
import { useAiChat } from "./logic";

const AiChat = () => {
  const {
    input,
    loading,
    messages,
    handleFieldChange,
    sendMessage,
    newChat,
    threads,
    sidebarOpen,
    setSidebarOpen,
    openThread,
    threadId,
    scrollRef,
  } = useAiChat();

  const onKeyDown = (e) => {
    if (e.key === "Enter" && !e.shiftKey) {
      e.preventDefault();
      e.currentTarget.form?.dispatchEvent(
        new Event("submit", { cancelable: true, bubbles: true })
      );
    }
  };

  return (
    <div className="ai-chat-container">
      <div className="ai-chat-header">
        <div className="ai-chat-header-left">
          <button
            className="ai-chat-history-btn"
            onClick={() => setSidebarOpen(!sidebarOpen)}
          >
            <History size={16} /> History
          </button>
        </div>
        <h2 className="ai-chat-title">Pathfinder</h2>
        <button
          className="ai-chat-new-btn"
          onClick={newChat}
          disabled={loading}
        >
          <Plus size={16} /> New chat
        </button>
      </div>

      {/* Sidebar */}
      <aside className={`ai-chat-sidebar ${sidebarOpen ? "open" : ""}`}>
        <div className="ai-chat-sidebar-header">
          <div>Recent chats</div>
          <button
            className="ai-chat-sidebar-close"
            onClick={() => setSidebarOpen(false)}
          >
            x
          </button>
        </div>
        <div className="ai-chat-thread-list">
          {threads.length === 0 && (
            <div className="ai-chat-thread-empty">No conversations yet.</div>
          )}
          {threads.map((t) => (
            <button
              key={t.id}
              className={`ai-thread-item ${
                String(t.id) === String(threadId) ? "active" : ""
              }`}
              onClick={() => openThread(t.id)}
            >
              <div className="ai-thread-title">{t.title || "Untitled"}</div>
              <div className="ai-thread-preview">{t.preview || "—"}</div>
              <div className="ai-thread-time">
                {t.last_message_at?.replace("T", " ").replace("Z", "")}
              </div>
            </button>
          ))}
        </div>
      </aside>

      <div className="ai-chat-messages" ref={scrollRef}>
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
            type="textarea"
            hint={"Ask me anything..."}
            value={input}
            onChangeListener={(e) => handleFieldChange(e.target.value)}
            onKeyDown={onKeyDown}
          />
          <Button
            disabled={loading}
            className={"ai-send-btn"}
            text={<SendHorizontal />}
          />
        </div>
      </form>
    </div>
  );
};

export default AiChat;
