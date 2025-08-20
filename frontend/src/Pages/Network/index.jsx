import "./style.css";
import SavedPath from "../../Components/SavedPath";

const Network = () => {
  return (
    <div className="network-body">
      <h1>Saved Paths</h1>
      <div className="network-paths">
        <SavedPath
          title={"Software Engineering"}
          tag={"Development"}
          subtitle={
            "Complete roadmap to becoming a full stack developer with React and Node.js"
          }
          progress_value={"75%"}
          saved_date={"8/18/2025"}
        />
        <SavedPath
          title={"Software Engineering"}
          tag={"Development"}
          subtitle={
            "Complete roadmap to becoming a full stack developer with React and Node.js"
          }
          progress_value={"75%"}
          saved_date={"8/18/2025"}
        />
        <SavedPath
          title={"Software Engineering"}
          tag={"Development"}
          subtitle={
            "Complete roadmap to becoming a full stack developer with React and Node.js"
          }
          progress_value={"75%"}
          saved_date={"8/18/2025"}
        />
      </div>
    </div>
  );
};

export default Network;
