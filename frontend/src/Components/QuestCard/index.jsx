import "./style.css";
import React, { useEffect, useMemo, useState } from "react";
import Button from "../Button";
import { useDispatch } from "react-redux";
import API from "../../Services/axios";
import { incrementAndPersist } from "../../Features/Skill/skillsSlice";

const QuestCard = ({
  id,
  pathId,
  title,
  subtitle,
  difficulty,
  duration,
  onMarkedDone,
}) => {
  const dispatch = useDispatch();
  const subText =
    typeof subtitle === "string" ? subtitle : subtitle?.message || "";

  const [status, setStatus] = useState("idle");
  const [remaining, setRemaining] = useState(0);

  const totalSeconds = useMemo(
    () => parseDurationToSeconds(duration),
    [duration]
  );

  useEffect(() => {
    if (isCompletedLocal("quest", pathId, id)) {
      setStatus("done");
      setRemaining(0);
    }
  }, [pathId, id]);

  useEffect(() => {
    if (status !== "running") return;
    if (remaining <= 0) {
      onMarkDone();
      return;
    }
    const t = setInterval(() => {
      setRemaining((s) => (s > 0 ? s - 1 : 0));
    }, 1000);
    return () => clearInterval(t);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [status, remaining]);

  const onStart = () => {
    const secs = totalSeconds;
    setRemaining(secs);
    setStatus("running");
  };

  const onMarkDone = async () => {
    if (status === "done") return;
    setStatus("done");
    setRemaining(0);

    persistCompletionLocal("quest", pathId, id);

    try {
      if (typeof onMarkedDone === "function") onMarkedDone();
    } catch {}

    const percent = await computeAdaptiveIncrement(pathId);
    if (pathId && percent > 0) {
      try {
        await dispatch(incrementAndPersist({ pathId, percent }));
      } catch (e) {}
    }
  };

  return (
    <div className={`quest-card${status === "done" ? " done" : ""}`}>
      <div className="quest-card-header">
        <h3 className={status === "done" ? "completed-title" : undefined}>
          {title}
        </h3>
      </div>
      <h4 className="quest-card-subtitle">{subText}</h4>
      <div className="quest-card-tags">
        <span>{difficulty}</span>
        <span>{duration}</span>
        {status === "running" && (
          <span className="quest-card-timer">{formatTime(remaining)}</span>
        )}
        {status === "done" && (
          <span className="quest-card-status">Completed</span>
        )}
      </div>
      <div className="quest-card-btn">
        {status === "idle" && (
          <Button
            className={"primary-button"}
            text={"Start Quest"}
            onClickListener={onStart}
          />
        )}
        {status === "running" && (
          <div className="quest-card-actions">
            <Button
              className={"secondary-button"}
              text={"Mark as done"}
              onClickListener={onMarkDone}
            />
          </div>
        )}
        {status === "done" && (
          <Button
            className={"secondary-button"}
            text={"Done"}
            disabled
            onClickListener={() => {}}
          />
        )}
      </div>
    </div>
  );
};

function parseDurationToSeconds(value) {
  if (value == null) return 0;
  if (typeof value === "number" && !isNaN(value)) {
    return Math.max(0, Math.round(value * 60));
  }
  if (typeof value !== "string") return 0;
  const v = value.trim().toLowerCase();

  if (/^\d{1,2}:\d{2}(:\d{2})?$/.test(v)) {
    const parts = v.split(":").map((p) => parseInt(p, 10));
    if (parts.length === 3) {
      const [h, m, s] = parts;
      return h * 3600 + m * 60 + s;
    } else {
      const [m, s] = parts;
      return m * 60 + s;
    }
  }

  const re =
    /(\d+(?:\.\d+)?)\s*(h|hr|hrs|hour|hours|m|min|mins|minute|minutes|s|sec|secs|second|seconds)\b/g;
  let match;
  let seconds = 0;
  while ((match = re.exec(v)) !== null) {
    const num = parseFloat(match[1]);
    const unit = match[2];
    if (["h", "hr", "hrs", "hour", "hours"].includes(unit))
      seconds += num * 3600;
    else if (["m", "min", "mins", "minute", "minutes"].includes(unit))
      seconds += num * 60;
    else if (["s", "sec", "secs", "second", "seconds"].includes(unit))
      seconds += num;
  }
  if (seconds > 0) return Math.round(seconds);

  const bare = parseFloat(v.match(/\d+(?:\.\d+)?/)?.[0]);
  if (!isNaN(bare)) return Math.round(bare * 60);
  return 0;
}

function formatTime(totalSeconds) {
  const s = Math.max(0, Math.floor(totalSeconds));
  const h = Math.floor(s / 3600);
  const m = Math.floor((s % 3600) / 60);
  const sec = s % 60;
  if (h > 0)
    return `${h}:${String(m).padStart(2, "0")}:${String(sec).padStart(2, "0")}`;
  return `${m}:${String(sec).padStart(2, "0")}`;
}

export default QuestCard;

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

function isCompletedLocal(type, pathId, id) {
  return getCompletedLocal(type, pathId).has(String(id));
}

function persistCompletionLocal(type, pathId, id) {
  try {
    const set = getCompletedLocal(type, pathId);
    set.add(String(id));
    localStorage.setItem(lsKey(type, pathId), JSON.stringify(Array.from(set)));
  } catch {}
}

async function computeAdaptiveIncrement(pathId) {
  try {
    const [questsRes, probsRes, skillsRes] = await Promise.all([
      API.get(`/user/quests/${pathId}`),
      API.get(`/user/problems/${pathId}`),
      API.get(`/user/skills/${pathId}`),
    ]);
    const totalTasks =
      (questsRes.data?.length || 0) + (probsRes.data?.length || 0);
    if (totalTasks <= 0) return 0;

    const skills = Array.isArray(skillsRes.data) ? skillsRes.data : [];
    const avg = skills.length
      ? skills.reduce((sum, s) => sum + (Number(s.value) || 0), 0) /
        skills.length
      : 0;

    const doneQ = getCompletedLocal("quest", pathId).size;
    const doneP = getCompletedLocal("problem", pathId).size;
    const remaining = Math.max(1, totalTasks - (doneQ + doneP));
    const delta = Math.ceil((100 - avg) / remaining);
    return Math.max(1, delta);
  } catch (e) {
    return 3;
  }
}
