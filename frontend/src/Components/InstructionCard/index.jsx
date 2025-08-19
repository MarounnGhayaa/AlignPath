import "./style.css";

const InstructionCard = ({ title, paragraph }) => {
  return (
    <div className="landing-instruction">
      <h3>{title}</h3>
      <p>{paragraph}</p>
    </div>
  );
};

export default InstructionCard;
