import "./style.css";
import Button from "../../Components/Button";
import { useNavigate, useParams } from "react-router-dom";
import { useEffect, useState } from "react";
import API from "../../Services/axios";
import { useDispatch, useSelector } from "react-redux";
import FloatingChatbot from "../../Components/FloatingChatbot";
import { incrementAndPersist } from "../../Features/Skill/skillsSlice";

const SolveProblem = () => {
  const navigate = useNavigate();
  const { problemId } = useParams();
  const [problem, setProblem] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const registerState = useSelector((state) => state.register) || {};
  const token = registerState.token || localStorage.getItem("token");
  const [selected, setSelected] = useState("");
  const [feedback, setFeedback] = useState("");
  const [done, setDone] = useState(false);
  const dispatch = useDispatch();

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
        const prob = {
          ...response.data,
          options: [
            response.data.first_answer,
            response.data.second_answer,
            response.data.third_answer,
          ],
        };
        setProblem(prob);
        if (isCompletedLocal("problem", response.data.path_id, problemId)) {
          setDone(true);
          setFeedback("correct");
        }
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
    const msg =
      typeof error === "string" ? error : error?.message || "Unknown error";
    return <div className="solve-problem-body">Error: {msg}</div>;
  }

  if (!problem) {
    return <div className="solve-problem-body">No problem found.</div>;
  }

  const { title = "", question = "", options = [], points = 0 } = problem;

  // eslint-disable-next-line no-unused-vars
  const pointsToPercent = (p) => {
    const val = Number(p) || 0;
    return Math.max(1, Math.round(val / 20));
  };

  const onSubmit = async () => {
    if (!selected || done) return;
    if (selected !== problem.correct_answer) {
      setFeedback("incorrect");
      return;
    }
    setFeedback("correct");
    setDone(true);

    const pathId = problem.path_id;
    if (pathId) {
      persistCompletionLocal("problem", pathId, problemId);
      const percent = await computeAdaptiveIncrement(pathId);
      await dispatch(incrementAndPersist({ pathId, percent }));
    }
    navigate("/pathNested", { state: { pathId, initialTab: "Problems" } });
  };

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
              <label
                key={index}
                className={`solve-problem-radio ${
                  feedback === "incorrect" && selected === option
                    ? "incorrect"
                    : ""
                }`}
              >
                <input
                  type="radio"
                  name="option"
                  value={option}
                  checked={selected === option}
                  onChange={(e) => setSelected(e.target.value)}
                  disabled={done}
                />
                <strong>{option}</strong>
              </label>
            ))}
          </section>

          {feedback === "incorrect" && (
            <div className="solve-problem-feedback incorrect">
              Incorrect. Try again.
            </div>
          )}
          {done && (
            <div className="solve-problem-feedback correct">
              Correct! Problem completed.
            </div>
          )}

          <div style={{ marginTop: 20 }}>
            {done ? (
              <Button
                text={"Completed"}
                className={"secondary-button"}
                onClickListener={() => {}}
                disabled
              />
            ) : (
              <Button
                text={"Submit Answer"}
                className={"primary-button"}
                onClickListener={onSubmit}
              />
            )}
          </div>
        </div>
      </div>

      <FloatingChatbot />
    </div>
  );
};

export default SolveProblem;

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

function isCompletedLocal(type, pathId, id) {
  return getCompletedLocal(type, pathId).has(String(id));
}

function persistCompletionLocal(type, pathId, id) {
  try {
    const set = getCompletedLocal(type, pathId);
    set.add(String(id));
    localStorage.setItem(lsKey(type, pathId), JSON.stringify(Array.from(set)));
  } catch {}
}

async function computeAdaptiveIncrement(pathId) {
  try {
    const [questsRes, probsRes, skillsRes] = await Promise.all([
      API.get(`/user/quests/${pathId}`),
      API.get(`/user/problems/${pathId}`),
      API.get(`/user/skills/${pathId}`),
    ]);
    const totalTasks =
      (questsRes.data?.length || 0) + (probsRes.data?.length || 0);
    if (totalTasks <= 0) return 0;

    const skills = Array.isArray(skillsRes.data) ? skillsRes.data : [];
    const avg = skills.length
      ? skills.reduce((sum, s) => sum + (Number(s.value) || 0), 0) /
        skills.length
      : 0;

    const doneQ = getCompletedLocal("quest", pathId).size;
    const doneP = getCompletedLocal("problem", pathId).size;
    const remaining = Math.max(1, totalTasks - (doneQ + doneP));
    const delta = Math.ceil((100 - avg) / remaining);
    return Math.max(1, delta);
  } catch (e) {
    return 3;
  }
}
