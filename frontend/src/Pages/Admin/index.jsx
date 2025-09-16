import "./style.css";
import Logo from "../../Assets/LogoOrangeNoBg.png";
import Button from "../../Components/Button";
import { Link, useLocation, useNavigate } from "react-router-dom";
import { useEffect, useState } from "react";
import API from "../../Services/axios";
import { Trash2 } from "lucide-react";

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

  const formatDay = (str) => {
    try {
      if (!str) return "";
      const d = new Date(str);
      if (isNaN(d.getTime())) return String(str);
      return d.toLocaleDateString(undefined, {
        weekday: "short",
        year: "numeric",
        month: "short",
        day: "2-digit",
      });
    } catch (e) {
      return String(str);
    }
  };

  const [confirmUser, setConfirmUser] = useState(null);
  const [toast, setToast] = useState(null);

  const handleConfirmDelete = async () => {
    const userId = confirmUser?.userId;
    if (!userId) return;
    try {
      await API.delete(`admin/users/${userId}`);
      setAnalyses((prev) => prev.filter((x) => x.user_id !== userId));
      setToast({ type: "success", message: "User deleted successfully." });
    } catch (err) {
      setToast({
        type: "error",
        message: `Failed to delete user: ${
          err?.response?.data?.message || err.message
        }`,
      });
    } finally {
      setConfirmUser(null);
    }
  };

  useEffect(() => {
    if (!toast) return;
    const id = setTimeout(() => setToast(null), 3000);
    return () => clearTimeout(id);
  }, [toast]);

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
            localStorage.removeItem("aiChatThreadId");
            localStorage.removeItem("aiChatUserId");
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
                    <th>User & Email</th>
                    <th>Summary</th>
                    <th>Delete User</th>
                  </tr>
                </thead>
                <tbody>
                  {analyses.map((a) => (
                    <tr key={a.id}>
                      <td className="day-cell">{formatDay(a.day)}</td>
                      <td className="user-thread-cell">
                        <div>
                          <span className="user-id">
                            {a?.user?.username ||
                              (a.user_id
                                ? `User ${a.user_id}`
                                : "Deleted user")}
                          </span>
                        </div>
                        <div>
                          <span className="user-email">
                            Email: {a?.user?.email || "Not available"}
                          </span>
                        </div>
                      </td>
                      <td className="summary-cell">{a.summary}</td>
                      <td>
                        <button
                          className="delete-icon-btn"
                          title="Delete user"
                          onClick={() =>
                            setConfirmUser({
                              userId: a.user_id,
                              userLabel:
                                a?.user?.email ||
                                a?.user?.username ||
                                (a.user_id
                                  ? `User ${a.user_id}`
                                  : "Deleted user"),
                            })
                          }
                        >
                          <Trash2 size={24} />
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <div className="no-data-message">No analyses found.</div>
          ))}
      </div>

      {toast && (
        <div
          className={`toast ${
            toast.type === "error" ? "toast-error" : "toast-success"
          }`}
        >
          {toast.message}
        </div>
      )}

      {confirmUser && (
        <div className="modal-overlay" role="dialog" aria-modal="true">
          <div className="modal">
            <h3 className="modal-title">Confirm Deletion</h3>
            <p className="modal-body">
              Are you sure you want to delete{" "}
              <strong>{confirmUser.userLabel}</strong>?
            </p>
            <div className="modal-actions">
              <button
                className="btn btn-secondary"
                onClick={() => setConfirmUser(null)}
              >
                Cancel
              </button>
              <button className="btn btn-danger" onClick={handleConfirmDelete}>
                Delete
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  );
};

export default AdminDashboard;
