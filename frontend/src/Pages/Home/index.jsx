import "./style.css";
import Recommendation from "../../Components/Recommendation";
import Statistic from "../../Components/Statistic";
import AiChat from "../AiChat";

const Home = () => {
  return (
    <div className="homePage-body">
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
        <div className="homePage-stats">
          <Statistic value={"50%"} statTitle={"Career Exploration"} />
          <Statistic value={"20%"} statTitle={"Mentorship Exploration"} />
          <Statistic value={"30%"} statTitle={"Skills Learned"} />
        </div>
      </div>
      <div className="homePage-chatbot">
        <AiChat />
      </div>
    </div>
  );
};

export default Home;
