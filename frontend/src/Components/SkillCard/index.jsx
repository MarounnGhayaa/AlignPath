import "./style.css";
import ProgressBar from "../ProgressBar";

const SkillCard = ({ title, value }) => {
  return (
    <div className="skill-card">
      <div className="skill-card-title">
        <h3>{title}</h3>
        <strong>{value}%</strong>
      </div>
      <ProgressBar progress={value} />
    </div>
  );
};

export default SkillCard;
