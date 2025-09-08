import "./style.css";
import Button from "../../Components/Button";
import { useNavigate, useParams } from "react-router-dom";
import { useEffect, useState } from "react";
import API from "../../Services/axios";
import { useSelector } from "react-redux";

const SolveProblem = () => {
  const navigate = useNavigate();
  const { problemId } = useParams();
  const [problem, setProblem] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const auth = useSelector((state) => state.auth) || {};
  const token = auth.token || localStorage.getItem("token");

  useEffect(() => {
    const fetchProblem = async () => {
      try {
        if (!token) {
          setError("Unauthorized. Please log in again.");
          setLoading(false);
          return;
        }

        if (!problemId) {
          setError("Problem ID is missing.");
          setLoading(false);
          return;
        }

        const response = await API.get(`/user/problems/${problemId}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        setProblem(response.data);
      } catch (err) {
        console.error("Error fetching problem:", err);
        setError("Failed to load problem.");
      } finally {
        setLoading(false);
      }
    };

    fetchProblem();
  }, [token, problemId]);

  if (loading) {
    return <div className="solve-problem-body">Loading problem...</div>;
  }

  if (error) {
    return <div className="solve-problem-body">Error: {error}</div>;
  }

  if (!problem) {
    return <div className="solve-problem-body">No problem found.</div>;
  }

  return (
    <div className="solve-problem-body">
      <div className="solve-problem-container">
        <header className="solve-problem-header">
          <h2>{problem.title}</h2>
          <h2>/{problem.points}</h2>
        </header>
        <div className="solve-problem-content">
          <section className="solve-problem-subtitle">
            <strong>{problem.question}</strong>
          </section>
          <section className="solve-problem-options">
            {problem.options &&
              problem.options.map((option, index) => (
                <label key={index} className="solve-problem-radio">
                  <input type="radio" name="option" value={option} />
                  <strong>{option}</strong>
                </label>
              ))}
          </section>
          <Button
            text={"Submit Answer"}
            className={"primary-button"}
            onClickListener={() => navigate(-1)}
          />
        </div>
      </div>
    </div>
  );
};

export default SolveProblem;
