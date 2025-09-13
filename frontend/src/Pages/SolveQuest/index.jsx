import "./style.css";
import Button from "../../Components/Button";
import { useLocation, useNavigate } from "react-router-dom";
import FloatingChatbot from "../../Components/FloatingChatbot";
import { useDispatch } from "react-redux";
import { incrementAndPersist } from "../../Features/Skill/skillsSlice";

const SolveQuest = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const dispatch = useDispatch();
  const { pathId, difficulty } = location.state || {};

  const difficultyToPercent = (d) => {
    const key = String(d || "").toLowerCase();
    if (key.includes("easy")) return 2;
    if (key.includes("medium")) return 4;
    if (key.includes("hard")) return 6;
    return 3; // default
  };

  const handleDone = async () => {
    if (pathId) {
      await dispatch(incrementAndPersist({ pathId, percent: difficultyToPercent(difficulty) }));
    }
    navigate(-1);
  };

  return (
    <div className="solve-quest-body">
      <div className="solve-quest-container">
        <header className="solve-quest-header">
          <h2>Quest Title</h2>
          <Button text={"Mark as Done"} className={"primary-button"} onClickListener={handleDone} />
        </header>
        <div className="solve-quest-content">
          <section className="solve-quest-subtitle">
            <strong>
              Design and develop scalable, maintainable, and secure software
              systems that meet real-world user needs while adapting to evolving
              technologies
            </strong>
          </section>
        </div>
      </div>
      <FloatingChatbot />
    </div>
  );
};

export default SolveQuest;
