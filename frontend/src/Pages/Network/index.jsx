import "./style.css";
import { User, Send, Search, Mic, Square } from "lucide-react";
import { useRef, useEffect } from "react";
import useNetwork from "./logic";

const Network = () => {
  const {
    selectedPerson,
    chatHistory,
    currentMessage,
    searchTerm,
    loadingList,
    loadingChats,
    error,
    iAmMentor,
    myId,
    recording,
    transcribing,
    micError,
    toggleRecording,
    setSearchTerm,
    setCurrentMessage,
    handlePersonSelect,
    handleSendMessage,
    filteredPeople,
    formatTime,
  } = useNetwork();

  const messagesWrapRef = useRef(null);
  const messagesEndRef = useRef(null);
  const prevThreadIdRef = useRef(null);

  const threadLen = selectedPerson
    ? chatHistory[selectedPerson.id]?.length || 0
    : 0;

  useEffect(() => {
    const el = messagesWrapRef.current;
    if (!el) return;

    const threadId = selectedPerson?.id ?? null;
    const changed = prevThreadIdRef.current !== threadId;
    const nearBottom = el.scrollHeight - el.scrollTop - el.clientHeight < 80;

    if (changed || nearBottom) {
      messagesEndRef.current?.scrollIntoView({
        behavior: changed ? "auto" : "smooth",
        block: "end",
      });
    }

    prevThreadIdRef.current = threadId;
  }, [selectedPerson?.id, threadLen]);
  // --------------------------

  return (
    <div className="network-container">
      <div className="mentors-sidebar">
        <div className="sidebar-header">
          <h2>{iAmMentor ? "People" : "Mentors"}</h2>
          <div className="search-container">
            <Search className="search-icon" size={16} />
            <input
              type="text"
              placeholder={iAmMentor ? "Search people..." : "Search mentors..."}
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="search-input"
            />
          </div>
        </div>

        {error && <div className="error-banner">{error}</div>}

        {loadingList ? (
          <div className="loading">
            Loading {iAmMentor ? "people" : "mentors"}…
          </div>
        ) : (
          <div className="mentors-list">
            {filteredPeople.map((person) => (
              <div
                key={person.id}
                className={`mentor-item ${
                  selectedPerson?.id === person.id ? "selected" : ""
                }`}
                onClick={() => handlePersonSelect(person)}
              >
                <div className="mentor-avatar">
                  <User size={32} />
                </div>
                <div className="mentor-info">
                  <div className="mentor-name">
                    {person.name}
                    {iAmMentor && person.role && (
                      <span className="role-pill"> {person.role}</span>
                    )}
                  </div>
                  <div className="mentor-position">{person.position}</div>
                  <div className="mentor-skills">
                    {(person.expertise || []).slice(0, 2).join(", ")}
                    {person.expertise &&
                      person.expertise.length > 2 &&
                      ` +${person.expertise.length - 2}`}
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      <div className="chat-section">
        {selectedPerson ? (
          <>
            <div className="chat-header">
              <div className="chat-mentor-info">
                <User size={40} />
                <div>
                  <h3>{selectedPerson.name}</h3>
                </div>
              </div>
            </div>

            {loadingChats && !chatHistory[selectedPerson.id] ? (
              <div className="chat-messages loading">Loading chat…</div>
            ) : (
              <div className="chat-messages" ref={messagesWrapRef}>
                {chatHistory[selectedPerson.id]?.map((message) => {
                  const outgoing = message.sender_id === myId;
                  return (
                    <div
                      key={message.id}
                      className={`message ${
                        outgoing ? "user-message" : "mentor-message"
                      }`}
                    >
                      <div className="message-content">
                        <p>{message.message}</p>
                        <span className="message-time">
                          {formatTime(message.timestamp)}
                        </span>
                      </div>
                    </div>
                  );
                })}
                {/* anchor for auto-scroll */}
                <div ref={messagesEndRef} />
              </div>
            )}

            <div className="chat-input">
              <input
                type="text"
                placeholder={
                  transcribing
                    ? "Transcribing…"
                    : "Type your message or use the mic…"
                }
                value={currentMessage}
                onChange={(e) => setCurrentMessage(e.target.value)}
                onKeyDown={(e) => e.key === "Enter" && handleSendMessage()}
                disabled={transcribing}
              />

              <button
                onClick={toggleRecording}
                title={recording ? "Stop recording" : "Start voice input"}
                aria-pressed={recording}
                className={recording ? "mic-btn recording" : "mic-btn"}
                disabled={transcribing}
              >
                {recording ? <Square size={18} /> : <Mic size={18} />}
              </button>

              <button
                onClick={handleSendMessage}
                disabled={!currentMessage.trim() || transcribing}
                title="Send"
              >
                <Send size={20} />
              </button>
            </div>
            {micError && <div className="error-banner">{micError}</div>}
          </>
        ) : (
          <div className="no-chat-selected">
            <User size={64} opacity={0.3} />
            <h3>Select someone to start chatting</h3>
            <p>Choose from the list on the left to begin your conversation.</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default Network;
