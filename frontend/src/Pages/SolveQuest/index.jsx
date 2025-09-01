import "./style.css";
import Button from "../../Components/Button";
import { useNavigate } from "react-router-dom";

const SolveQuest = () => {
  const navigate = useNavigate();

  return (
    <div className="solve-quest-body">
      <div className="solve-quest-container">
        <header className="solve-quest-header">
          <h2>Quest Title</h2>
          <Button
            text={"Mark as Done"}
            className={"primary-button"}
            onClickListener={() => navigate(-1)}
          />
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
    </div>
  );
};

export default SolveQuest;
