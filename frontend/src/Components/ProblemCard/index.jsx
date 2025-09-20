import "./style.css";
import Button from "../Button";
import { useNavigate } from "react-router-dom";
import React, { useEffect, useState } from "react";

const ProblemCard = ({ id, pathId, title, subtitle, points }) => {
  const navigate = useNavigate();
  const [done, setDone] = useState(false);

  useEffect(() => {
    if (!pathId) return;
    const set = getCompletedLocal("problem", pathId);
    setDone(set.has(String(id)));
  }, [pathId, id]);

  return (
    <div className={`problem-card${done ? " done" : ""}`}>
      <h3 className={done ? "completed-title" : undefined}>{title}</h3>
      <h4>{subtitle}</h4>
      <div className="problem-card-tags">
        <span>{points} points</span>
        {done && <span className="problem-card-status">Completed</span>}
      </div>
      <div className="problem-card-btn">
        <Button
          className={done ? "secondary-button" : "primary-button"}
          text={done ? "Done" : "Solve Problem"}
          onClickListener={() => !done && navigate(`/solveProblem/${id}`)}
          disabled={done}
        />
      </div>
    </div>
  );
};

function lsKey(type, pathId) {
  return `ap_completed_${type}_${pathId}`;
}

function getCompletedLocal(type, pathId) {
  try {
    const raw = localStorage.getItem(lsKey(type, pathId));
    const arr = raw ? JSON.parse(raw) : [];
    if (Array.isArray(arr)) return new Set(arr.map(String));
    return new Set();
  } catch {
    return new Set();
  }
}

export default ProblemCard;
