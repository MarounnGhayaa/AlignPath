import "./style.css";
import QuestCard from "../../Components/QuestCard";
import { useEffect, useState } from "react";
import API from "../../Services/axios";
import { useSelector } from "react-redux";

const Quest = ({ pathId }) => {
  const [quests, setQuests] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const registerState = useSelector((state) => state.register) || {};
  const token = registerState.token || localStorage.getItem("token");

  useEffect(() => {
    const fetchQuests = async () => {
      try {
        if (!token) {
          setError("Unauthorized. Please log in again.");
          setLoading(false);
          return;
        }

        if (!pathId) {
          setError("Path ID is missing.");
          setLoading(false);
          return;
        }

        const response = await API.get(`/user/quests/${pathId}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        setQuests(response.data);
      } catch (err) {
        console.error("Error fetching quests:", err);
        setError("Failed to load quests.");
      } finally {
        setLoading(false);
      }
    };

    fetchQuests();
  }, [token, pathId]);

  if (loading) {
    return <div className="quest-body">Loading quests...</div>;
  }

  if (error) {
    return <div className="quest-body">Error: {error}</div>;
  }

  return (
    <div className="quest-body">
      <h1>Learning Quests</h1>
      <p>Complete structured learning experiences to advance your skills</p>
      <div className="quest-body-row">
        {quests.length > 0 ? (
          quests.map((quest) => (
            <QuestCard
              key={quest.id}
              title={quest.title}
              subtitle={quest.subtitle}
              difficulty={quest.difficulty}
              duration={quest.duration}
            />
          ))
        ) : (
          <p>No quests found for this path.</p>
        )}
      </div>
    </div>
  );
};

export default Quest;
