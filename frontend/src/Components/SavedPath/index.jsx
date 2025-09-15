import "./style.css";
import Button from "../Button";
import ProgressBar from "../ProgressBar";
import React, { useEffect, useState } from "react";
import API from "../../Services/axios";

const SavedPath = ({
  pathId,
  title,
  subtitle,
  progress_value,
  saved_date,
  onClickListener,
}) => {
  const [earnedPoints, setEarnedPoints] = useState(0);

  useEffect(() => {
    const computePoints = async () => {
      if (!pathId) return;
      try {
        const { data: problems } = await API.get(`/user/problems/${pathId}`);
        const completed = getCompletedLocal("problem", pathId);
        const total = (problems || []).reduce((sum, p) => {
          return completed.has(String(p.id)) ? sum + (Number(p.points) || 0) : sum;
        }, 0);
        setEarnedPoints(total);
      } catch (e) {
        setEarnedPoints(0);
      }
    };
    computePoints();
  }, [pathId]);

  return (
    <div className="path-card">
      <div className="path-card-header">
        <h3>{title}</h3>
        <h4>{earnedPoints} pts</h4>
      </div>
      <h4 className="path-card-subtitle">{subtitle}</h4>
      <div className="path-card-progress">
        <strong>Progress</strong>
        <strong>{progress_value}%</strong>
      </div>
      <div className="path-card-progress-bar">
        <ProgressBar progress={progress_value} />
      </div>
      <div className="path-card-footer">
        <h5>Saved: {saved_date}</h5>
        <Button
          className={"primary-button"}
          text={"Continue"}
          onClickListener={onClickListener}
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

export default SavedPath;
