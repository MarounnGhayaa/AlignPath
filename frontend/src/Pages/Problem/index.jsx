import "./style.css";
import ProblemCard from "../../Components/ProblemCard";

const Problem = () => {
  return (
    <div className="problem-body">
      <h1>Practice Problems</h1>
      <p>Solve coding challenges and earn XP points</p>
      <div className="problem-body-row">
        <ProblemCard />
        <ProblemCard />
        <ProblemCard />
        <ProblemCard />
      </div>
    </div>
  );
};

export default Problem;
