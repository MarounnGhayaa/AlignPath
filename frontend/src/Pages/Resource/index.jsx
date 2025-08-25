import "./style.css";
import ResourceCard from "../../Components/ResourceCard";

const Resource = () => {
  return (
    <div className="resource-body">
      <h1>Learning Resources</h1>
      <p>Curated materials to support your learning journey</p>
      <div className="resource-body-row">
        <ResourceCard title={"Documentation"} type={"documentation"} />
        <ResourceCard title={"Video tutorials"} type={"video"} />
        <ResourceCard title={"Community"} type={"community"} />
      </div>
    </div>
  );
};

export default Resource;
