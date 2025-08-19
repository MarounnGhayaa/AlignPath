import "./style.css";
import Logo from "../../Assets/LogoOrangeNoBg.png";
import Button from "../../Components/Button";

const Landing = () => {
  return (
    <body className="landing-body">
      <header className="landing-nav">
        <div className="landing-logo">
          <img src={Logo} alt="AlignPath Logo" className="landing-logo-img" />
          <strong>
            Align<span>Path</span>
          </strong>
        </div>
        <Button text={"Sign In"} className="primary-button" />
      </header>
    </body>
  );
};

export default Landing;
