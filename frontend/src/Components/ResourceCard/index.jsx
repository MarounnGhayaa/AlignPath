import "./style.css";
import Button from "../Button";

const ResourceCard = ({ title, type }) => {
  let cardDescription = " ";
  let buttonText = " ";

  if (type === "documentation") {
    cardDescription =
      "Official documentation and guides for the technologies you're learning.";
    buttonText = "Browse Documentation";
  } else if (type === "video") {
    cardDescription =
      "Step-by-step video tutorials covering key concepts and implementations.";
    buttonText = "Watch Videos";
  } else if (type === "community") {
    cardDescription =
      "Connect with other learners and experienced developers in your field.";
    buttonText = "Join Community";
  }
  return (
    <div className="resource-card">
      <h3>{title}</h3>
      <h4>{cardDescription}</h4>
      <div className="resource-card-btn">
        <Button className={"primary-button"} text={buttonText} />
      </div>
    </div>
  );
};

export default ResourceCard;
