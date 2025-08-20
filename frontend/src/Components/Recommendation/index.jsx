import "./style.css";
import Button from "../Button";
import { Briefcase } from "lucide-react";

const Recommendation = ({ title, description }) => {
  return (
    <div className="recommendation-card">
      <h4>{title}</h4>
      <Briefcase />
      <p>{description}</p>
      <Button text={"Explore"} className={"primary-button"} />
    </div>
  );
};

export default Recommendation;
