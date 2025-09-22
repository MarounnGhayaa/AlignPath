import "./style.css";
import Button from "../Button";
import { Briefcase } from "lucide-react";
import { useNavigate } from "react-router-dom";

const Recommendation = ({ title, description, recommendationId }) => {
  const navigate = useNavigate();
  const descText =
    typeof description === "string"
      ? description
      : description?.message || "";

  return (
    <div className="recommendation-card">
      <Briefcase />
      <h4>{title}</h4>
      <h6>{descText}</h6>
      <Button
        text={"Explore"}
        className={"primary-button"}
        onClickListener={() => {
          navigate("/explore", {
            state: { title, description, recommendationId },
          });
        }}
      />
    </div>
  );
};

export default Recommendation;
