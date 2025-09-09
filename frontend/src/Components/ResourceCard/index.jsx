import "./style.css";
import Button from "../Button";

const ResourceCard = ({ title, description, type, url }) => {
  let buttonText = "Open";
  if (type === "documentation") buttonText = "Browse Documentation";
  else if (type === "video") buttonText = "Watch Video";
  else if (type === "community") buttonText = "Join Community";

  return (
    <div className="resource-card">
      <h3>{title}</h3>
      {description && <h4>{description}</h4>}

      <div className="resource-card-btn">
        {url ? (
          <a href={url} target="_blank" rel="noreferrer">
            <Button className={"primary-button"} text={buttonText} />
          </a>
        ) : (
          <Button className={"primary-button"} text={buttonText} />
        )}
      </div>
    </div>
  );
};

export default ResourceCard;
