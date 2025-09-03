import "./style.css";
import Button from "../../Components/Button";
import OrangeLogo from "../../Assets/LogoOrangeNoBg.png";
import WhiteLogo from "../../Assets/LogoWhiteNoBg.png";
import { useNavigate } from "react-router-dom";

const Explore = () => {
  const navigate = useNavigate();

  return (
    <div className="explore-body">
      <div className="explore-heading">
        <Button
          insiders={"←  "}
          text={"Title"}
          className={"explore-left-button"}
          onClickListener={() => {
            navigate("/home");
          }}
        />
      </div>
      <div className="explore-description">
        <p>
          Build innovative software solutions, work with cutting-edge
          technologies, and shape the digital future. Software engineers are the
          architects of our technological world.
        </p>
      </div>
      <div className="explore-row-btns">
        <Button
          className={"secondary-button"}
          text={"Connect with Mentors"}
          onClickListener={() => {
            navigate("/network");
          }}
        />
        <Button
          className={"primary-button"}
          text={"Save Path"}
          onClickListener={() => {
            navigate("/path");
          }}
        />
      </div>
      <footer className="explore-designed-row">
        <img src={WhiteLogo} alt="AlignPath Logo" className="explore-WLogo" />
        <img src={OrangeLogo} alt="AlignPath Logo" className="explore-OLogo" />
        <img src={WhiteLogo} alt="AlignPath Logo" className="explore-WLogo" />
        <img src={OrangeLogo} alt="AlignPath Logo" className="explore-OLogo" />
        <img src={WhiteLogo} alt="AlignPath Logo" className="explore-WLogo" />
      </footer>
    </div>
  );
};

export default Explore;
