import "./style.css";
import SkillCard from "../../Components/SkillCard";
import { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { fetchSkills } from "../../Features/Skill/skillsSlice";

const Skill = ({ pathId }) => {
  const dispatch = useDispatch();
  const entry = useSelector((state) => state.skills?.byPathId?.[pathId]) || {
    items: [],
    loading: true,
    error: null,
  };
  const { items: skills, loading, error } = entry;

  useEffect(() => {
    if (pathId) {
      dispatch(fetchSkills(pathId));
    }
  }, [dispatch, pathId]);

  if (loading) {
    return <div className="skill-body">Loading skills...</div>;
  }

  if (error) {
    const msg = typeof error === "string" ? error : error?.message || "Unknown error";
    return <div className="skill-body">Error: {msg}</div>;
  }

  return (
    <div className="skill-body">
      <h1>Skill development</h1>
      <p>Track your progress across different technical skills</p>
      <div className="skill-body-row">
        {skills.length > 0 ? (
          skills.map((skill) => (
            <SkillCard key={skill.id} title={skill.name} value={skill.value} />
          ))
        ) : (
          <p>No skills found for this path.</p>
        )}
      </div>
    </div>
  );
};

export default Skill;
