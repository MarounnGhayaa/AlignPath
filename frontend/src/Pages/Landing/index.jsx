import "./style.css";
import Logo from "../../Assets/LogoOrangeNoBg.png";
import Button from "../../Components/Button";
import InstructionCard from "../../Components/InstructionCard";

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
      <section className="landing-work-section">
        <h1>How AlignPath Works</h1>
        <h2>
          Three simple steps to unlock your career potential and create a
          personalized roadmap for success.
        </h2>
        <div className="landing-row-instructions">
          <InstructionCard
            title={"DISCOVER & ASSESS"}
            paragraph={
              "Complete our comprehensive assessment to identify your skills, interests, values, and career preferences. Our AI analyzes your unique profile to understand what drives you."
            }
          />
          <InstructionCard
            title={"EXPLORE & PLAN"}
            paragraph={
              "Receive personalized career recommendations and detailed roadmaps. Explore different paths, understand requirements, and see how your current skills align with your dream roles."
            }
          />
          <InstructionCard
            title={"EXECUTE & GROW"}
            paragraph={
              "Follow your custom action plan with guided learning resources, skill development tracks, and milestone tracking. Get ongoing support as you progress toward your goals."
            }
          />
        </div>
      </section>
    </body>
  );
};

export default Landing;
