import "./style.css";
import { useState } from "react";
import Quest from "../../Pages/Quest";
import Problem from "../../Pages/Problem";
import Resource from "../../Pages/Resource";
import Skill from "../../Pages/Skill";

const SubNavBar = ({ pathId, initialTab = "Quests" }) => {
  const [activeTab, setActiveTab] = useState(initialTab || "Quests");

  const tabs = ["Quests", "Problems", "Skills", "Resources"];

  return (
    <div className="sub-body">
      <div className="subNavbar">
        {tabs.map((tab) => (
          <button
            key={tab}
            className={`subNavItem ${activeTab === tab ? "active" : ""}`}
            onClick={() => setActiveTab(tab)}
          >
            {tab}
          </button>
        ))}
      </div>

      <div className="tabContent">
        {activeTab === "Quests" && <Quest pathId={pathId} />}
        {activeTab === "Problems" && <Problem pathId={pathId} />}
        {activeTab === "Skills" && <Skill pathId={pathId} />}
        {activeTab === "Resources" && <Resource pathId={pathId} />}
      </div>
    </div>
  );
};

export default SubNavBar;
