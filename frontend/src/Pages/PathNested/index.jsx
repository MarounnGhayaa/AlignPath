import "./style.css";
import SubNavBar from "../../Components/SubNavBar";
const PathNested = () => {
  return (
    <div className="pathNested-body">
      <h1>
        <span>←</span>Title
      </h1>
      <SubNavBar />
    </div>
  );
};

export default PathNested;
