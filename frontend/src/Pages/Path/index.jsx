import "./style.css";
import { useEffect, useState } from "react";
import { useSelector } from "react-redux";
import API from "../../Services/axios";
import SavedPath from "../../Components/SavedPath";

const Path = () => {
  const [savedPaths, setSavedPaths] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const auth = useSelector((state) => state.auth) || {};
  const token = auth.token || localStorage.getItem("token");

  useEffect(() => {
    const fetchSavedPaths = async () => {
      try {
        if (!token) {
          setError("Unauthorized. Please log in again.");
          setLoading(false);
          return;
        }

        const response = await API.get(`/user/paths`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        setSavedPaths(response.data);
      } catch (err) {
        console.error("Error fetching saved paths:", err);
        setError("Failed to load saved paths.");
      } finally {
        setLoading(false);
      }
    };

    fetchSavedPaths();
  }, [token]);

  if (loading) {
    return <div className="path-body">Loading saved paths...</div>;
  }

  if (error) {
    return <div className="path-body">Error: {error}</div>;
  }

  return (
    <div className="path-body">
      <h1>Saved Paths</h1>
      <div className="path-paths">
        {savedPaths.length > 0 ? (
          savedPaths.map((path, index) => (
            <SavedPath
              key={index}
              title={path.title}
              tag={"Path"}
              subtitle={path.tag || "No description provided."}
              progress_value={path.progress_percentage}
              saved_date={new Date(path.date_saved).toLocaleDateString()}
            />
          ))
        ) : (
          <p>No saved paths found.</p>
        )}
      </div>
    </div>
  );
};

export default Path;
