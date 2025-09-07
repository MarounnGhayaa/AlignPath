import "./style.css";
import Button from "../Button";

const QuestCard = ({ title, subtitle, difficulty, duration }) => {
  return (
    <div className="quest-card">
      <div className="quest-card-header">
        <h3>{title}</h3>
      </div>
      <h4 className="quest-card-subtitle">{subtitle}</h4>
      <div className="quest-card-tags">
        <span>{difficulty}</span>
        <span>{duration}</span>
      </div>
      <div className="quest-card-btn">
        <Button className={"primary-button"} text={"Start Quest"} />
      </div>
    </div>
  );
};

export default QuestCard;
