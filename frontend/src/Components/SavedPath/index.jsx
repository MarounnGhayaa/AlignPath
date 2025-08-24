import "./style.css";
import Button from "../Button";
import { useNavigate } from "react-router-dom";
import ProgressBar from "../ProgressBar";

const SavedPath = ({ title, tag, subtitle, progress_value, saved_date }) => {
  const navigate = useNavigate();

  return (
    <div className="path-card">
      <div className="path-card-header">
        <h3>{title}</h3>
        <h4>{tag}</h4>
      </div>
      <h4 className="path-card-subtitle">{subtitle}</h4>
      <div className="path-card-progress">
        <strong>Progress</strong>
        <strong>{progress_value}%</strong>
      </div>
      <div className="path-card-progress-bar">
        <ProgressBar progress={progress_value} />
      </div>
      <div className="path-card-footer">
        <h5>Saved: {saved_date}</h5>
        <Button
          className={"primary-button"}
          text={"Continue"}
          onClickListener={() => {
            navigate("/pathNested");
          }}
        />
      </div>
    </div>
  );
};

export default SavedPath;
