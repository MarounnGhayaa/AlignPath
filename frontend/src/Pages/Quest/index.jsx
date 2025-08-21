import "./style.css";
import QuestCard from "../../Components/QuestCard";

const Quest = () => {
  return (
    <div className="quest-body">
      <h1>Learning Quests</h1>
      <p>Complete structured learning experiences to advance your skills</p>
      <div className="quest-body-row">
        <QuestCard />
        <QuestCard />
        <QuestCard />
        <QuestCard />
      </div>
    </div>
  );
};

export default Quest;
