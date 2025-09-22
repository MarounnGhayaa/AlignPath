import "./style.css";
import Button from "../Button";
import ProgressBar from "../ProgressBar";

const SavedPath = ({
  pathId,
  title,
  subtitle,
  progress_value,
  saved_date,
  onClickListener,
}) => {
  return (
    <div className="path-card">
      <div className="path-card-header">
        <h3>{title}</h3>
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
          onClickListener={onClickListener}
        />
      </div>
    </div>
  );
};

export default SavedPath;
