import "./style.css";
import ProblemCard from "../../Components/ProblemCard";
import { useEffect, useState } from "react";
import API from "../../Services/axios";
import { useSelector } from "react-redux";

const Problem = ({ pathId }) => {
  const [problems, setProblems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const registerState = useSelector((state) => state.register) || {};
  const token = registerState.token || localStorage.getItem("token");

  useEffect(() => {
    const fetchProblems = async () => {
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

        const response = await API.get(`/user/problems/${pathId}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        setProblems(response.data);
      } catch (err) {
        console.error("Error fetching problems:", err);
        setError("Failed to load problems.");
      } finally {
        setLoading(false);
      }
    };

    fetchProblems();
  }, [token, pathId]);

  if (loading) {
    return <div className="problem-body">Loading problems...</div>;
  }

  if (error) {
    return <div className="problem-body">Error: {error}</div>;
  }

  return (
    <div className="problem-body">
      <h1>Practice Problems</h1>
      <p>Solve coding challenges and earn XP points</p>
      <div className="problem-body-row">
        {problems.length > 0 ? (
          problems.map((problem) => (
            <ProblemCard
              key={problem.id}
              id={problem.id}
              title={problem.title}
              subtitle={problem.subtitle}
              points={problem.points}
            />
          ))
        ) : (
          <p>No problems found.</p>
        )}
      </div>
    </div>
  );
};

export default Problem;
