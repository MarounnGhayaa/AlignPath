import "./style.css";
import Recommendation from "../../Components/Recommendation";
import Statistic from "../../Components/Statistic";
import FloatingChatbot from "../../Components/FloatingChatbot";
import { useEffect, useState } from "react";
import { useSelector } from "react-redux";
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
  const [userName, setUserName] = useState("Student");
  const registerState = useSelector((state) => state.register) || {};

  useEffect(() => {
    const user =
      registerState.user || JSON.parse(localStorage.getItem("user") || "null");
    if (user) {
      if (user.name) {
        setUserName(user.name);
      } else if (user.username) {
        setUserName(user.username);
      } else if (user.email) {
        const emailPrefix = user.email.split("@")[0];
        setUserName(emailPrefix);
      }
    }

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
  }, [registerState.user]);

  return (
    <div className="homePage-body">
      <div className="homePage">
        <h1>Welcome, {userName}!</h1>
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
          ) : loading ? (
            <p>Loading recommendations...</p>
          ) : error ? (
            <p>Error: {error.message}</p>
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
      <FloatingChatbot />
    </div>
  );
};

export default Home;
