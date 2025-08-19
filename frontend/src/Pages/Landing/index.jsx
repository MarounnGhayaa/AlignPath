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
      <main className="landing-hero">
        <div className="landing-hero-text">
          <h1>Navigate Your Career Journey with Confidence</h1>
          <h2>
            AlignPath connects your skills, passions, and goals to create
            personalized career roadmaps that adapt as you grow.
          </h2>
          <div className="landing-row-btns">
            <Button text={"Get Started"} className="primary-button" />
            <Button text={"Success Stories"} className="secondary-button" />
          </div>
        </div>

        <div className="landing-her-img"></div>
      </main>
    </body>
  );
};

export default Landing;
