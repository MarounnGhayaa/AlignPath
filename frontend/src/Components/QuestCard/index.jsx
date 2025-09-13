import "./style.css";
import Button from "../Button";
import { useNavigate } from "react-router-dom";

const QuestCard = ({ id, pathId, title, subtitle, difficulty, duration }) => {
  const navigate = useNavigate();
  const subText = typeof subtitle === "string" ? subtitle : subtitle?.message || "";
  return (
    <div className="quest-card">
      <div className="quest-card-header">
        <h3>{title}</h3>
      </div>
      <h4 className="quest-card-subtitle">{subText}</h4>
      <div className="quest-card-tags">
        <span>{difficulty}</span>
        <span>{duration}</span>
      </div>
      <div className="quest-card-btn">
        <Button
          className={"primary-button"}
          text={"Start Quest"}
          onClickListener={() =>
            navigate(`/solveQuest`, { state: { pathId, questId: id, difficulty } })
          }
        />
      </div>
    </div>
  );
};

export default QuestCard;
