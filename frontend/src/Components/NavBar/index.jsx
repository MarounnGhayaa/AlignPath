import "./style.css";
import Logo from "../../Assets/LogoOrangeNoBg.png";
import Button from "../../Components/Button";
import { Link, useLocation } from "react-router-dom";

const NavBar = () => {
  const location = useLocation();
  const isActive = (path) => location.pathname === path;
  return (
    <header className="sharedNav">
      <div className="sharedNav-logo">
        <img src={Logo} alt="AlignPath Logo" className="sharedNav-logo-img" />
        <strong>
          Align<span>Path</span>
        </strong>
      </div>
      <div>
        <Link
          to="/home"
          className={
            isActive("/home") ? "sharedNav-links active" : "sharedNav-links"
          }
        >
          Dashboard
        </Link>
        <Link
          to="/network"
          className={
            isActive("/network") ? "sharedNav-links active" : "sharedNav-links"
          }
        >
          Network
        </Link>
        <Link
          to="/path"
          className={
            isActive("/path") ? "sharedNav-links active" : "sharedNav-links"
          }
        >
          Path
        </Link>
        <Link
          to="/profile"
          className={
            isActive("/profile") ? "sharedNav-links active" : "sharedNav-links"
          }
        >
          Profile
        </Link>
      </div>
      <Button text={"Log Out"} className="primary-button" />
    </header>
  );
};

export default NavBar;
