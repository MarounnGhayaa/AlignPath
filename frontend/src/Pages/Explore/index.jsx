import "./style.css";
import React, { useState } from "react";
import Button from "../../Components/Button";
import OrangeLogo from "../../Assets/LogoOrangeNoBg.png";
import WhiteLogo from "../../Assets/LogoWhiteNoBg.png";
import { useNavigate, useLocation } from "react-router-dom";
import API from "../../Services/axios";
import { useSelector } from "react-redux";
import { useEffect } from "react";
import FloatingChatbot from "../../Components/FloatingChatbot";

const Explore = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const { title, description, recommendationId } = location.state || {};
  const descriptionText =
    typeof description === "string"
      ? description
      : description?.message || "Description";
  const registerState = useSelector((state) => state.register) || {};
  const token = registerState.token || localStorage.getItem("token");
  const [isSaving, setIsSaving] = useState(false);

  useEffect(() => {
    console.log("Explore page rendered.");
    console.log("location.state:", location.state);
    console.log("recommendationId at render:", recommendationId);
  }, [location.state, recommendationId]);

  const handleSavePath = async () => {
    console.log("handleSavePath called.");
    console.log("recommendationId in handleSavePath:", recommendationId);
    try {
      setIsSaving(true);
      if (!token) {
        console.error("Unauthorized. Please log in again.");
        setIsSaving(false);
        return;
      }

      if (!recommendationId) {
        console.error("Recommendation ID is missing.");
        setIsSaving(false);
        return;
      }

      const acceptPathResponse = await API.post(
        `user/accept-path`,
        {
          recommendation_id: recommendationId,
        },
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      const { path_id, career_name } = acceptPathResponse.data;

      await API.post(
        `user/generate-quests-and-problems`,
        {
          career: career_name,
          path_id: path_id,
        },
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      navigate("/path");
    } catch (error) {
      console.error("Error saving path or generating quests:", error);
    } finally {
      setIsSaving(false);
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
        <p>{descriptionText}</p>
      </div>
      <div className="explore-row-btns">
        <Button
          className={"secondary-button"}
          text={"Connect with Mentors"}
          onClickListener={() => {
            navigate("/network");
          }}
        />
        <div className="save-path-wrapper">
          <div className="sticky-note" role="note" aria-live="polite">
            <div className="sticky-note-heading">Heads up!</div>
            <div className="sticky-note-body">
              saving a career takes some time because we are generating some
              quests, problems, resources and skills you'll acquire... So get
              ready!
            </div>
            <span className="sticky-arrow" aria-hidden="true" />
          </div>
          <Button
            className={"primary-button"}
            text={isSaving ? "Saving..." : "Save Path"}
            disabled={isSaving}
            onClickListener={handleSavePath}
          />
        </div>
      </div>
      <footer className="explore-designed-row">
        <img src={WhiteLogo} alt="AlignPath Logo" className="explore-WLogo" />
        <img src={OrangeLogo} alt="AlignPath Logo" className="explore-OLogo" />
        <img src={WhiteLogo} alt="AlignPath Logo" className="explore-WLogo" />
        <img src={OrangeLogo} alt="AlignPath Logo" className="explore-OLogo" />
        <img src={WhiteLogo} alt="AlignPath Logo" className="explore-WLogo" />
      </footer>
      <FloatingChatbot />
    </div>
  );
};

export default Explore;
