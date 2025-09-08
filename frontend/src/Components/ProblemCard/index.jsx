import "./style.css";
import Button from "../Button";
import { useNavigate } from "react-router-dom";

const ProblemCard = ({ id, title, subtitle, difficulty, points }) => {
  const navigate = useNavigate();

  return (
    <div className="problem-card">
      <h3>{title}</h3>
      <h4>{subtitle}</h4>
      <div className="problem-card-tags">
        <span>{points} points</span>
      </div>
      <div className="problem-card-btn">
        <Button
          className={"primary-button"}
          text={"Solve Problem"}
          onClickListener={() => navigate(`/solveProblem/${id}`)}
        />
      </div>
    </div>
  );
};

export default ProblemCard;
