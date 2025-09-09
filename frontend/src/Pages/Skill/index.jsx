import "./style.css";
import SkillCard from "../../Components/SkillCard";
import { useEffect, useState } from "react";
import API from "../../Services/axios";
import { useSelector } from "react-redux";

const Skill = ({ pathId }) => {
  const [skills, setSkills] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const registerState = useSelector((state) => state.register) || {};
  const token = registerState.token || localStorage.getItem("token");

  useEffect(() => {
    const fetchSkills = async () => {
      try {
        if (!token) {
          setError("Unauthorized. Please log in again.");
          setLoading(false);
          return;
        }

        if (!pathId) {
          setError("Path ID is missing.");
          setLoading(false);
          return;
        }

        const response = await API.get(`/user/skills/${pathId}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        setSkills(response.data);
      } catch (err) {
        console.error("Error fetching skills:", err);
        setError("Failed to load skills.");
      } finally {
        setLoading(false);
      }
    };

    fetchSkills();
  }, [token, pathId]);

  if (loading) {
    return <div className="skill-body">Loading skills...</div>;
  }

  if (error) {
    return <div className="skill-body">Error: {error}</div>;
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
