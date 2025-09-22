import "./style.css";
import ResourceCard from "../../Components/ResourceCard";
import { useEffect, useState } from "react";
import API from "../../Services/axios";
import { useSelector } from "react-redux";

const Resource = ({ pathId }) => {
  const [resources, setResources] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const registerState = useSelector((state) => state.register) || {};
  const token = registerState.token || localStorage.getItem("token");

  useEffect(() => {
    const fetchResources = async () => {
      try {
        if (!token) {
          setError("Unauthorized. Please log in again.");
          setLoading(false);
          return;
        }

        if (!pathId) {
          setError("Path ID is missing.");
          setLoading(false);
          return;
        }

        const response = await API.get(`/user/resources/${pathId}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        setResources(response.data);
      } catch (err) {
        console.error("Error fetching resources:", err);
        setError("Failed to load resources.");
      } finally {
        setLoading(false);
      }
    };

    fetchResources();
  }, [token, pathId]);

  if (loading) {
    return <div className="resource-body">Loading resources...</div>;
  }

  if (error) {
    const msg = typeof error === "string" ? error : error?.message || "Unknown error";
    return <div className="resource-body">Error: {msg}</div>;
  }

  return (
    <div className="resource-body">
      <h1>Learning Resources</h1>
      <p>Curated materials to support your learning journey</p>

      <div className="resource-body-row">
        {resources.length > 0 ? (
          resources.map((res) => (
            <ResourceCard
              key={res.id}
              title={res.name}
              description={res.description}
              type={res.type}
              url={res.url}
            />
          ))
        ) : (
          <p>No resources found for this path.</p>
        )}
      </div>
    </div>
  );
};

export default Resource;
