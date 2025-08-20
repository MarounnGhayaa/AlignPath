import "./style.css";
import Recommendation from "../../Components/Recommendation";

const Home = () => {
  return (
    <div className="homePage">
      <h1>Welcome, Student!</h1>
      <div className="homePage-careers">
        <Recommendation
          title={"Software Engineering"}
          description={'"Develop software applications and systems"'}
        />
        <Recommendation
          title={"Graphic Designer"}
          description={'"Create visual fronts, designs and user interfaces"'}
        />
        <Recommendation
          title={"Marketing Manager"}
          description={'"Plan and execute strategies for the markets"'}
        />
      </div>
    </div>
  );
};

export default Home;
