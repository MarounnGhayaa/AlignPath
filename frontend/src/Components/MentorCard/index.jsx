import "./style.css";
import Button from "../Button";
import { Calendar } from "lucide-react";
import { MessageCircle } from "lucide-react";
import { useNavigate } from "react-router-dom";

const MentorCard = ({ image, name, position, skills }) => {
  const navigate = useNavigate();

  return (
    <div className="mentor-card">
      <div className="mentor-header">
        <div className="mentor-img">
          <span>{image}</span>
        </div>
        <div className="mentor-titles">
          <h3>{name}</h3>
          <strong>{position}</strong>
        </div>
      </div>
      <h4>Expertise</h4>
      <div className="mentor-skills">
        {skills.map((skill, index) => (
          <span key={index} className="mentor-skill">
            {skill}{" "}
          </span>
        ))}
      </div>
      <div className="mentor-row-btns">
        <Button
          className={"primary-button"}
          text={
            <strong className="mentor-btn-txt">
              <MessageCircle />
              Message
            </strong>
          }
          onClickListener={() => {
            navigate("/chat");
          }}
        />
        <Button className={"secondary-button"} text={<Calendar />} />
      </div>
    </div>
  );
};

export default MentorCard;
