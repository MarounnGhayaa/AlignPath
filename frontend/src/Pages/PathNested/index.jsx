import "./style.css";
import SubNavBar from "../../Components/SubNavBar";
import Button from "../../Components/Button";
import { useNavigate, useLocation } from "react-router-dom";
import FloatingChatbot from "../../Components/FloatingChatbot";

const PathNested = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const { pathId, title, initialTab } = location.state || {};

  return (
    <div className="pathNested-body">
      <h1>
        <Button
          insiders={"←  "}
          text={title || "Title"}
          className={"pathNested-left-button"}
          onClickListener={() => {
            navigate("/path");
          }}
        />
      </h1>
      <SubNavBar pathId={pathId} initialTab={initialTab} />
      <FloatingChatbot />
    </div>
  );
};

export default PathNested;
