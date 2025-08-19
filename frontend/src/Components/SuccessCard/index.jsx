import "./style.css";

const SuccessCard = ({ story, name, position }) => {
  return (
    <div className="landing-story">
      <p>{story}</p>
      <h3>{name}</h3>
      <h4>{position}</h4>
    </div>
  );
};

export default SuccessCard;
