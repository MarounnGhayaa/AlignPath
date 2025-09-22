import "./style.css";

const InstructionCard = ({ icon, title, paragraph }) => {
  return (
    <div className="landing-instruction">
      <div className="landing-instruction-icon-container">
        <span className="landing-instruction-icon">{icon}</span>
      </div>
      <h3>{title}</h3>
      <h6>{paragraph}</h6>
    </div>
  );
};

export default InstructionCard;
