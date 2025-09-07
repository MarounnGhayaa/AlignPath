import "./style.css";
import Recommendation from "../../Components/Recommendation";
import Statistic from "../../Components/Statistic";
import AiChat from "../AiChat";
import { useEffect, useState } from "react";
import API from "../../Services/axios";

const Home = () => {
  const getRecommendations = async (token) => {
    try {
      const response = await API.get(`user/recommendations`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });
      return response.data;
    } catch (error) {
      console.error("Error fetching recommendations:", error);
      throw error;
    }
  };
  const [recommendations, setRecommendations] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchRecommendations = async () => {
      try {
        const token = localStorage.getItem("token");
        const data = await getRecommendations(token);
        setRecommendations(data);
      } catch (err) {
        setError(err);
      } finally {
        setLoading(false);
      }
    };

    fetchRecommendations();
  }, []);

  return (
    <div className="homePage-body">
      <div className="homePage">
        <h1>Welcome, Student!</h1>
        <div className="homePage-careers">
          {recommendations.length > 0 ? (
            recommendations.map((rec) => (
              <Recommendation
                key={rec.id}
                title={rec.career_name}
                description={rec.description}
                recommendationId={rec.id}
              />
            ))
          ) : (
            <p>No recommendations found.</p>
          )}
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
