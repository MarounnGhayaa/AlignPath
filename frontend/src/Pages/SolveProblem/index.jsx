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

        const response = await API.get(`/user/problem/${problemId}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        setProblem({
          ...response.data,
          options: [
            response.data.first_answer,
            response.data.second_answer,
            response.data.third_answer,
          ],
        });
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

  const { title = "", question = "", options = [], points = 0 } = problem;

  return (
    <div className="solve-problem-body">
      <div className="solve-problem-container">
        <header className="solve-problem-header">
          <h2>{title}</h2>
          <h2>/{points}</h2>
        </header>
        <div className="solve-problem-content">
          <section className="solve-problem-subtitle">
            <strong>{question}</strong>
          </section>
          <section className="solve-problem-options">
            {options.map((option, index) => (
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
