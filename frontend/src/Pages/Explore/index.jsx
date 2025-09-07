import "./style.css";
import React from "react";
import Button from "../../Components/Button";
import OrangeLogo from "../../Assets/LogoOrangeNoBg.png";
import WhiteLogo from "../../Assets/LogoWhiteNoBg.png";
import { useNavigate, useLocation } from "react-router-dom";

const Explore = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const { title, description } = location.state || {};

  return (
    <div className="explore-body">
      <div className="explore-heading">
        <Button
          insiders={"←  "}
          text={title || "Title"}
          className={"explore-left-button"}
          onClickListener={() => {
            navigate("/home");
          }}
        />
      </div>
      <div className="explore-description">
        <p>{description || "Description"}</p>
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
