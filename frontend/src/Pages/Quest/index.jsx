import "./style.css";
import QuestCard from "../../Components/QuestCard";
import { useEffect, useState, useCallback } from "react";
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
        const data = Array.isArray(response.data) ? response.data : [];
        // Sort so that completed quests are shown last
        const completed = getCompletedLocal("quest", pathId);
        const notDone = [];
        const done = [];
        for (const q of data) {
          (completed.has(String(q.id)) ? done : notDone).push(q);
        }
        setQuests([...notDone, ...done]);
      } catch (err) {
        console.error("Error fetching quests:", err);
        setError("Failed to load quests.");
      } finally {
        setLoading(false);
      }
    };

    fetchQuests();
  }, [token, pathId]);

  const handleMarkedDone = useCallback((questId) => {
    setQuests((prev) => {
      const idx = prev.findIndex((q) => q.id === questId);
      if (idx === -1) return prev;
      const arr = prev.slice();
      const [item] = arr.splice(idx, 1);
      arr.push(item);
      return arr;
    });
  }, []);

  if (loading) {
    return <div className="quest-body">Loading quests...</div>;
  }

  if (error) {
    const msg =
      typeof error === "string" ? error : error?.message || "Unknown error";
    return <div className="quest-body">Error: {msg}</div>;
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
              id={quest.id}
              pathId={pathId}
              title={quest.title}
              subtitle={quest.subtitle}
              difficulty={quest.difficulty}
              duration={quest.duration}
              onMarkedDone={() => handleMarkedDone(quest.id)}
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

function lsKey(type, pathId) {
  return `ap_completed_${type}_${pathId}`;
}

function getCompletedLocal(type, pathId) {
  try {
    const raw = localStorage.getItem(lsKey(type, pathId));
    const arr = raw ? JSON.parse(raw) : [];
    if (Array.isArray(arr)) return new Set(arr.map(String));
    return new Set();
  } catch {
    return new Set();
  }
}
