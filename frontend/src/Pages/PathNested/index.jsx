import "./style.css";
import SubNavBar from "../../Components/SubNavBar";
import Button from "../../Components/Button";
import { useNavigate } from "react-router-dom";

const PathNested = () => {
  const navigate = useNavigate();

  return (
    <div className="pathNested-body">
      <h1>
        <Button
          insiders={"←  "}
          text={"Title"}
          className={"pathNested-left-button"}
          onClickListener={() => {
            navigate("/path");
          }}
        />
      </h1>
      <SubNavBar />
    </div>
  );
};

export default PathNested;
