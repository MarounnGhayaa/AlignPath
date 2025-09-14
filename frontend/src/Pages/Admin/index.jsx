import "./style.css";
import Logo from "../../Assets/LogoOrangeNoBg.png";
import Button from "../../Components/Button";
import { Link, useLocation, useNavigate } from "react-router-dom";

const AdminDashboard = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const isActive = (path) => location.pathname === path;
  return (
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
          Analyses
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
  );
};

export default AdminDashboard;
