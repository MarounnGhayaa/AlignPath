import "./style.css";
import { User, Send, Search } from "lucide-react";
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
    setSearchTerm,
    setCurrentMessage,
    handlePersonSelect,
    handleSendMessage,
    filteredPeople,
    formatTime,
  } = useNetwork();

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
              <div className="chat-messages">
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
              </div>
            )}

            <div className="chat-input">
              <input
                type="text"
                placeholder="Type your message..."
                value={currentMessage}
                onChange={(e) => setCurrentMessage(e.target.value)}
                onKeyDown={(e) => e.key === "Enter" && handleSendMessage()}
              />
              <button
                onClick={handleSendMessage}
                disabled={!currentMessage.trim()}
                title="Send"
              >
                <Send size={20} />
              </button>
            </div>
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
