import "./style.css";
import Button from "../../Components/Button";
import { useNavigate } from "react-router-dom";

const SolveProblem = () => {
  const navigate = useNavigate();

  return (
    <div className="solve-problem-body">
      <div className="solve-problem-container">
        <header className="solve-problem-header">
          <h2>Problem Title</h2>
          <h2>/100</h2>
        </header>
        <div className="solve-problem-content">
          <section className="solve-problem-subtitle">
            <strong>
              “How can we ensure software quality and reliability while managing
              complexity in large-scale systems?”
            </strong>
          </section>
          <section className="solve-problem-options">
            <label className="solve-problem-radio">
              <input type="radio" name="option1" value="" />
              <strong>Option 1</strong>
            </label>
            <label className="solve-problem-radio">
              <input type="radio" name="option2" value="" />
              <strong>Option 2</strong>
            </label>
            <label className="solve-problem-radio">
              <input type="radio" name="option3" value="" />
              <strong>Option 3</strong>
            </label>
          </section>
          <Button
            text={"Submit Answer"}
            className={"primary-button"}
            onClickListener={() => navigate(-1)}
          />
        </div>
      </div>
    </div>
  );
};

export default SolveProblem;
