import "./style.css";
import React from "react";
import Button from "../../Components/Button";
import OrangeLogo from "../../Assets/LogoOrangeNoBg.png";
import WhiteLogo from "../../Assets/LogoWhiteNoBg.png";
import { useNavigate, useLocation } from "react-router-dom";
import API from "../../Services/axios";
import { useSelector } from "react-redux";
import { useEffect } from "react";

const Explore = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const { title, description, recommendationId } = location.state || {};
  const auth = useSelector((state) => state.auth) || {};
  const token = auth.token || localStorage.getItem("token");

  useEffect(() => {
    console.log("Explore page rendered.");
    console.log("location.state:", location.state);
    console.log("recommendationId at render:", recommendationId);
  }, [location.state, recommendationId]);

  const handleSavePath = async () => {
    console.log("handleSavePath called.");
    console.log("recommendationId in handleSavePath:", recommendationId);
    try {
      if (!token) {
        console.error("Unauthorized. Please log in again.");
        return;
      }

      if (!recommendationId) {
        console.error("Recommendation ID is missing.");
        return;
      }

      await API.post(
        `user/ai/accept-path`,
        {
          recommendation_id: recommendationId,
        },
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      navigate("/path");
    } catch (error) {
      console.error("Error saving path:", error);
    }
  };

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
          onClickListener={handleSavePath}
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
