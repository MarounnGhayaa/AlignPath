import "./style.css";
import ResourceCard from "../../Components/ResourceCard";

const Resource = () => {
  return (
    <div className="resource-body">
      <h1>Learning Resources</h1>
      <p>Curated materials to support your learning journey</p>
      <div className="resource-body-row">
        <ResourceCard title={"doc"} type={"documentation"} />
        <ResourceCard title={"vid"} type={"video"} />
        <ResourceCard title={"comm"} type={"community"} />
      </div>
    </div>
  );
};

export default Resource;
