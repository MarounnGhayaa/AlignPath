import "./style.css";

const InstructionCard = ({ title, paragraph }) => {
  return (
    <div className="landing-instruction">
      <h3>{title}</h3>
      <h6>{paragraph}</h6>
    </div>
  );
};

export default InstructionCard;
