import "./style.css";
import Button from "../Button";
import { useNavigate } from "react-router-dom";

const QuestCard = () => {
  const navigate = useNavigate();

  return (
    <div className="quest-card">
      <h3>Introduction to programming</h3>
      <h4>Learn the basics of programming logic and problem solving.</h4>
      <div className="quest-card-tags">
        <span>Beginner</span>
        <span>2 hours</span>
      </div>
      <div className="quest-card-btn">
        <Button
          className={"primary-button"}
          text={"Start Quest"}
          onClickListener={() => navigate("/solveQuest")}
        />
      </div>
    </div>
  );
};

export default QuestCard;
