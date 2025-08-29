import "./style.css";
import Logo from "../../Assets/LogoOrangeNoBg.png";
import HeroIMG from "../../Assets/HeroImage.png";
import Button from "../../Components/Button";
import InstructionCard from "../../Components/InstructionCard";
import SuccessCard from "../../Components/SuccessCard";
import { useNavigate } from "react-router-dom";
import { Handshake, Notebook, Sprout } from "lucide-react";
import Footer from "../../Components/Footer";
import { HashLink } from "react-router-hash-link";

const Landing = () => {
  const navigate = useNavigate();

  return (
    <div className="landing-body">
      <header className="landing-nav">
        <div className="landing-logo">
          <img src={Logo} alt="AlignPath Logo" className="landing-logo-img" />
          <strong>
            Align<span>Path</span>
          </strong>
        </div>
        <div className="landing-nav-links">
          <a href="#how-it-works">
            <h3>How It Works</h3>
          </a>
          <a href="#success-stories">
            <h3>Success Stories</h3>
          </a>
        </div>
        <Button
          text={"Sign In"}
          className="primary-button"
          onClickListener={() => {
            navigate("/auth");
          }}
        />
      </header>
      <main className="landing-hero">
        <div className="landing-hero-text">
          <h1>Navigate Your Career Journey with Confidence</h1>
          <h2>
            AlignPath connects your skills, passions, and goals to create
            personalized career roadmaps that adapt as you grow.
          </h2>
          <div className="landing-row-btns">
            <Button
              text={"Get Started"}
              className="primary-button"
              onClickListener={() => {
                navigate("/auth");
              }}
            />
            <Button
              text={
                <HashLink
                  smooth
                  to="#land-footer"
                  className="secondary-button reach-out-decoration"
                >
                  Reach Out
                </HashLink>
              }
              className="secondary-button"
            />
          </div>
        </div>

        <div className="landing-hero-img-cont">
          <img
            src={HeroIMG}
            alt="AlignPath Logo"
            className="landing-hero-img"
          />
        </div>
      </main>
      <section className="landing-work-section" id="how-it-works">
        <h1>How AlignPath Works</h1>
        <h2>
          Three simple steps to unlock your career potential and create a
          personalized roadmap for success.
        </h2>
        <div className="landing-row-instructions">
          <InstructionCard
            icon={<Handshake />}
            title={"DISCOVER & ASSESS"}
            paragraph={
              "Complete our comprehensive assessment to identify your skills, interests, values, and career preferences. Our AI analyzes your unique profile to understand what drives you."
            }
          />
          <InstructionCard
            icon={<Notebook />}
            title={"EXPLORE & PLAN"}
            paragraph={
              "Receive personalized career recommendations and detailed roadmaps. Explore different paths, understand requirements, and see how your current skills align with your dream roles."
            }
          />
          <InstructionCard
            icon={<Sprout />}
            title={"EXECUTE & GROW"}
            paragraph={
              "Follow your custom action plan with guided learning resources, skill development tracks, and milestone tracking. Get ongoing support as you progress toward your goals."
            }
          />
        </div>
      </section>
      <section className="landing-story-section" id="success-stories">
        <h1>Success Stories</h1>
        <h2>Hear from others who transformed their careers with AlignPath</h2>
        <div className="landing-row-stories">
          <img
            src={Logo}
            alt="AlignPath Logo"
            className="landing-logo-img-sc"
          />
          <SuccessCard
            story={
              "'AlignPath helped me transition from marketing to UX design in just 8 months. The personalized roadmap and skill assessments were exactly what I needed to make a confident career change.'"
            }
            name={"Faouzia Jomaa"}
            position={"UX Designer at TechFlow"}
          />
          <SuccessCard
            story={
              "'As a recent graduate, I was overwhelmed by career options. AlignPath's assessment revealed strengths I didn't know I had and connected me with my dream job in data science.'"
            }
            name={"Jad Nader"}
            position={"Data Scientist at Minders"}
          />
          <img
            src={Logo}
            alt="AlignPath Logo"
            className="landing-logo-img-sc"
          />
        </div>
      </section>
      <footer id="land-footer" className="landing-footer-style">
        <Footer />
      </footer>
    </div>
  );
};

export default Landing;
