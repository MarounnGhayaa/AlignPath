import "./style.css";
import Button from "../../Components/Button";
import OrangeLogo from "../../Assets/LogoOrangeNoBg.png";
import WhiteLogo from "../../Assets/LogoWhiteNoBg.png";

const Explore = () => {
  return (
    <div className="explore-body">
      <div className="explore-heading">
        <h1>←</h1>
        <h1>Software Engineering</h1>
      </div>
      <div className="explore-description">
        <p>
          Build innovative software solutions, work with cutting-edge
          technologies, and shape the digital future. Software engineers are the
          architects of our technological world.
        </p>
      </div>
      <div className="explore-row-btns">
        <Button className={"primary-button"} text={"Save Path"} />
        <Button className={"secondary-button"} text={"Connect with Mentors"} />
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
