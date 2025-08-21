import "./style.css";
import Button from "../Button";
const SavedPath = ({ title, tag, subtitle, progress_value, saved_date }) => {
  return (
    <div className="path-card">
      <div className="path-card-header">
        <h3>{title}</h3>
        <h4>{tag}</h4>
      </div>
      <h4 className="path-card-subtitle">{subtitle}</h4>
      <div className="path-card-progress">
        <strong>Progress</strong>
        <strong>{progress_value}</strong>
      </div>
      <div className="path-card-footer">
        <h5>Saved: {saved_date}</h5>
        <Button className={"primary-button"} text={"Continue"} />
      </div>
    </div>
  );
};

export default SavedPath;
