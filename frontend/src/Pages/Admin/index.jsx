import "./style.css";
import Logo from "../../Assets/LogoOrangeNoBg.png";
import Button from "../../Components/Button";
import { Link, useLocation, useNavigate } from "react-router-dom";
import { useEffect, useState } from "react";
import API from "../../Services/axios";

const AdminDashboard = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const isActive = (path) => location.pathname === path;
  const getDailyAnalyses = async (token) => {
    try {
      const res = await API.get("admin/analyses", {
        headers: { Authorization: `Bearer ${token}` },
      });
      return res.data;
    } catch (error) {
      console.error("Error fetching daily analyses:", error);
      throw error;
    }
  };

  const [analyses, setAnalyses] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchAnalyses = async () => {
      try {
        const token = localStorage.getItem("token");
        const data = await getDailyAnalyses(token);
        setAnalyses(Array.isArray(data?.data) ? data.data : data);
      } catch (err) {
        setError(err);
      } finally {
        setLoading(false);
      }
    };

    fetchAnalyses();
  }, []);

  return (
    <>
      <header className="adminNav">
        <div className="adminNav-logo">
          <img src={Logo} alt="AlignPath Logo" className="adminNav-logo-img" />
          <strong>
            Align<span>Path</span>
          </strong>
        </div>
        <div>
          <Link
            to="/adminDashboard"
            className={
              isActive("/adminDashboard")
                ? "adminNav-links active"
                : "adminNav-links"
            }
          >
            Analytics
          </Link>
        </div>
        <Button
          text={"Log Out"}
          className="primary-button"
          onClickListener={() => {
            localStorage.removeItem("token");
            localStorage.removeItem("user");
            navigate("/");
          }}
        />
      </header>

      <div className="dashboard-content">
        <h1 className="dashboard-title">Daily Analytics</h1>

        {loading && (
          <div className="loading-message">Loading daily analytics...</div>
        )}
        {error && <div className="error-message">Error: {error.message}</div>}

        {!loading &&
          !error &&
          (analyses?.length ? (
            <div className="table-container">
              <table className="analyses-table">
                <thead>
                  <tr>
                    <th>Day</th>
                    <th>User & Thread</th>
                    <th>Summary</th>
                  </tr>
                </thead>
                <tbody>
                  {analyses.map((a) => (
                    <tr key={a.id}>
                      <td className="day-cell">{a.day}</td>
                      <td className="user-thread-cell">
                        <div>
                          <span className="user-id">User {a.user_id}</span>
                        </div>
                        <div>
                          <span className="thread-id">
                            Thread {a.thread_id}
                          </span>
                        </div>
                      </td>
                      <td className="summary-cell">{a.summary}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <div className="no-data-message">No analyses found.</div>
          ))}
      </div>
    </>
  );
};

export default AdminDashboard;
