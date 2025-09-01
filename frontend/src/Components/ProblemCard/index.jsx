import "./style.css";
import Button from "../Button";
import { useNavigate } from "react-router-dom";

const ProblemCard = () => {
  const navigate = useNavigate();

  return (
    <div className="problem-card">
      <h3>Array Manipulation Challenge</h3>
      <h4>Sort and filter arrays using various algorithms.</h4>
      <div className="problem-card-tags">
        <span>Algorithm</span>
        <span>100 points</span>
      </div>
      <div className="problem-card-btn">
        <Button
          className={"primary-button"}
          text={"Solve Problem"}
          onClickListener={() => navigate("/solveProblem")}
        />
      </div>
    </div>
  );
};

export default ProblemCard;
