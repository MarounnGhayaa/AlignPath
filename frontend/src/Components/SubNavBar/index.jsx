import "./style.css";
import { useState } from "react";
import Quest from "../../Pages/Quest";
import Problem from "../../Pages/Problem";
import Resource from "../../Pages/Resource";

const SubNavBar = () => {
  const [activeTab, setActiveTab] = useState("Quests");

  const tabs = ["Quests", "Problems", "Skills", "Resources"];

  return (
    <div>
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
        {activeTab === "Quests" && <Quest />}
        {activeTab === "Problems" && <Problem />}
        {activeTab === "Skills" && <h3>Skills</h3>}
        {activeTab === "Resources" && <Resource />}
      </div>
    </div>
  );
};

export default SubNavBar;
