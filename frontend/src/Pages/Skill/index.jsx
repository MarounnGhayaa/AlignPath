import "./style.css";
import SkillCard from "../../Components/SkillCard";

const Skill = () => {
  return (
    <div className="skill-body">
      <h1>Skill development</h1>
      <p>Track your progress across different technical skills</p>
      <div className="skill-body-row">
        <SkillCard title={"JavaScript"} value={"70"} />
        <SkillCard title={"React"} value={"80"} />
        <SkillCard title={"Laravel"} value={"50"} />
        <SkillCard title={"NestJS"} value={"30"} />
      </div>
    </div>
  );
};

export default Skill;
