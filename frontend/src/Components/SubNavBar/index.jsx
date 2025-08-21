import "./style.css";
import { useState } from "react";
import Quest from "../../Pages/Quest";

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
        {activeTab === "Problems" && <h3>Problems</h3>}
        {activeTab === "Skills" && <h3>Skills</h3>}
        {activeTab === "Resources" && <h3>Resources</h3>}
      </div>
    </div>
  );
};

export default SubNavBar;
