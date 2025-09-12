import "./style.css";
import { User, Send, Search } from "lucide-react";
import { useState, useEffect } from "react";

const Network = () => {
  const [mentors, setMentors] = useState([]);
  const [selectedMentor, setSelectedMentor] = useState(null);
  const [chatHistory, setChatHistory] = useState({});
  const [currentMessage, setCurrentMessage] = useState("");
  const [searchTerm, setSearchTerm] = useState("");

  // Mock function to fetch mentors from database
  useEffect(() => {
    // Replace this with your actual database call
    const fetchMentors = async () => {
      // Mock data - replace with actual API call
      const mockMentors = [
        {
          id: 1,
          name: "Merwen Gh",
          position: "Senior Software Engineer at Google",
          skills: ["React", "JavaScript", "OOP"],
          avatar: null,
          isOnline: true,
          lastSeen: new Date(),
        },
        {
          id: 2,
          name: "Sarah Johnson",
          position: "Full Stack Developer at Microsoft",
          skills: ["Node.js", "Python", "AWS"],
          avatar: null,
          isOnline: false,
          lastSeen: new Date(Date.now() - 2 * 60 * 60 * 1000), // 2 hours ago
        },
        {
          id: 3,
          name: "Ahmed Hassan",
          position: "DevOps Engineer at Amazon",
          skills: ["Docker", "Kubernetes", "CI/CD"],
          avatar: null,
          isOnline: true,
          lastSeen: new Date(),
        },
        {
          id: 4,
          name: "Emily Chen",
          position: "UI/UX Designer at Meta",
          skills: ["Figma", "Design Systems", "User Research"],
          avatar: null,
          isOnline: false,
          lastSeen: new Date(Date.now() - 24 * 60 * 60 * 1000), // 1 day ago
        },
      ];
      setMentors(mockMentors);
    };

    fetchMentors();
  }, []);

  // Mock function to fetch chat history
  const fetchChatHistory = async (mentorId) => {
    // Replace with actual database call
    const mockChats = {
      1: [
        {
          id: 1,
          senderId: 1,
          senderName: "Merwen Gh",
          message: "Hi! How can I help you today?",
          timestamp: new Date(Date.now() - 60 * 60 * 1000),
          isFromMentor: true,
        },
        {
          id: 2,
          senderId: "current_user",
          senderName: "You",
          message: "I need help with React hooks",
          timestamp: new Date(Date.now() - 55 * 60 * 1000),
          isFromMentor: false,
        },
        {
          id: 3,
          senderId: 1,
          senderName: "Merwen Gh",
          message:
            "Sure! What specific aspect of React hooks are you struggling with?",
          timestamp: new Date(Date.now() - 50 * 60 * 1000),
          isFromMentor: true,
        },
      ],
      2: [
        {
          id: 1,
          senderId: 2,
          senderName: "Sarah Johnson",
          message: "Hello! Welcome to the mentorship program!",
          timestamp: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000),
          isFromMentor: true,
        },
      ],
    };

    if (!chatHistory[mentorId]) {
      setChatHistory((prev) => ({
        ...prev,
        [mentorId]: mockChats[mentorId] || [],
      }));
    }
  };

  const handleMentorSelect = (mentor) => {
    setSelectedMentor(mentor);
    fetchChatHistory(mentor.id);
  };

  const handleSendMessage = () => {
    if (!currentMessage.trim() || !selectedMentor) return;

    const newMessage = {
      id: Date.now(),
      senderId: "current_user",
      senderName: "You",
      message: currentMessage,
      timestamp: new Date(),
      isFromMentor: false,
    };

    setChatHistory((prev) => ({
      ...prev,
      [selectedMentor.id]: [...(prev[selectedMentor.id] || []), newMessage],
    }));

    setCurrentMessage("");

    // Mock mentor response after a delay
    setTimeout(() => {
      const mentorResponse = {
        id: Date.now() + 1,
        senderId: selectedMentor.id,
        senderName: selectedMentor.name,
        message: "Thanks for your message! I'll get back to you soon.",
        timestamp: new Date(),
        isFromMentor: true,
      };

      setChatHistory((prev) => ({
        ...prev,
        [selectedMentor.id]: [
          ...(prev[selectedMentor.id] || []),
          mentorResponse,
        ],
      }));
    }, 2000);
  };

  const filteredMentors = mentors.filter(
    (mentor) =>
      mentor.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      mentor.skills.some((skill) =>
        skill.toLowerCase().includes(searchTerm.toLowerCase())
      )
  );

  const formatTime = (date) => {
    return new Intl.DateTimeFormat("en-US", {
      hour: "2-digit",
      minute: "2-digit",
    }).format(date);
  };

  const formatLastSeen = (date) => {
    const now = new Date();
    const diff = now - date;
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));

    if (hours < 1) return "Just now";
    if (hours < 24) return `${hours}h ago`;
    return `${days}d ago`;
  };

  return (
    <div className="network-container">
      {/* Sidebar */}
      <div className="mentors-sidebar">
        <div className="sidebar-header">
          <h2>Mentors</h2>
          <div className="search-container">
            <Search className="search-icon" size={16} />
            <input
              type="text"
              placeholder="Search mentors..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="search-input"
            />
          </div>
        </div>

        <div className="mentors-list">
          {filteredMentors.map((mentor) => (
            <div
              key={mentor.id}
              className={`mentor-item ${
                selectedMentor?.id === mentor.id ? "selected" : ""
              }`}
              onClick={() => handleMentorSelect(mentor)}
            >
              <div className="mentor-avatar">
                <User size={32} />
                <div
                  className={`status-indicator ${
                    mentor.isOnline ? "online" : "offline"
                  }`}
                ></div>
              </div>
              <div className="mentor-info">
                <div className="mentor-name">{mentor.name}</div>
                <div className="mentor-position">{mentor.position}</div>
                <div className="mentor-skills">
                  {mentor.skills.slice(0, 2).join(", ")}
                  {mentor.skills.length > 2 && ` +${mentor.skills.length - 2}`}
                </div>
                {!mentor.isOnline && (
                  <div className="last-seen">
                    Last seen {formatLastSeen(mentor.lastSeen)}
                  </div>
                )}
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Chat Section */}
      <div className="chat-section">
        {selectedMentor ? (
          <>
            <div className="chat-header">
              <div className="chat-mentor-info">
                <User size={40} />
                <div>
                  <h3>{selectedMentor.name}</h3>
                  <p
                    className={`status ${
                      selectedMentor.isOnline ? "online" : "offline"
                    }`}
                  >
                    {selectedMentor.isOnline
                      ? "Online"
                      : `Last seen ${formatLastSeen(selectedMentor.lastSeen)}`}
                  </p>
                </div>
              </div>
            </div>

            <div className="chat-messages">
              {chatHistory[selectedMentor.id]?.map((message) => (
                <div
                  key={message.id}
                  className={`message ${
                    message.isFromMentor ? "mentor-message" : "user-message"
                  }`}
                >
                  <div className="message-content">
                    <p>{message.message}</p>
                    <span className="message-time">
                      {formatTime(message.timestamp)}
                    </span>
                  </div>
                </div>
              ))}
            </div>

            <div className="chat-input">
              <input
                type="text"
                placeholder="Type your message..."
                value={currentMessage}
                onChange={(e) => setCurrentMessage(e.target.value)}
                onKeyPress={(e) => e.key === "Enter" && handleSendMessage()}
              />
              <button
                onClick={handleSendMessage}
                disabled={!currentMessage.trim()}
              >
                <Send size={20} />
              </button>
            </div>
          </>
        ) : (
          <div className="no-chat-selected">
            <User size={64} opacity={0.3} />
            <h3>Select a mentor to start chatting</h3>
            <p>
              Choose from the list of available mentors on the left to begin
              your conversation.
            </p>
          </div>
        )}
      </div>
    </div>
  );
};

export default Network;
