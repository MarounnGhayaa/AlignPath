import "./style.css";
import Button from "../Button";
import { Briefcase } from "lucide-react";
import { useNavigate } from "react-router-dom";

const Recommendation = ({ title, description }) => {
  const navigate = useNavigate();

  return (
    <div className="recommendation-card">
      <h4>{title}</h4>
      <Briefcase />
      <h6>{description}</h6>
      <Button
        text={"Explore"}
        className={"primary-button"}
        onClickListener={() => {
          navigate("/explore");
        }}
      />
    </div>
  );
};

export default Recommendation;
