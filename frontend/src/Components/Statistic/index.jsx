import "./style.css";

const Statistic = ({ value, statTitle }) => {
  return (
    <div className="landing-stat">
      <h2>{value}</h2>
      <h3>{statTitle}</h3>
    </div>
  );
};

export default Statistic;
