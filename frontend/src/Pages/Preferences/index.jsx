import "./style.css";
import Button from "../../Components/Button";
import { usePreferencesLogic } from "./logic";

const Preferences = () => {
  const {
    skills,
    interests,
    values,
    careers,
    errorMessage,
    loading,
    handleFieldChange,
    handleSavePreferences,
    recommendCareers,
  } = usePreferencesLogic();

  const handleSubmit = (e) => {
    e.preventDefault();
    handleSavePreferences();
  };

  const toggleValue = (field, value, currentArray) => {
    const newArray = currentArray.includes(value)
      ? currentArray.filter((item) => item !== value)
      : [...currentArray, value];
    handleFieldChange(field, newArray);
  };

  return (
    <div className="pref-body">
      <div className="pref-container">
        <form className="pref-form" onSubmit={handleSubmit}>
          <h1>Tell Us More</h1>
          <h5>This form is needed to define your experience</h5>

          <div>
            <label className="pref-label">Skills</label>
            <div className="checkbox-group">
              {[
                "Technical Skills",
                "Soft Skills",
                "Analytical Skills",
                "Creative Skills",
                "Communication Skills",
              ].map((item) => (
                <label key={item} className="checkbox-label">
                  <input
                    type="checkbox"
                    checked={skills.includes(item.toLowerCase())}
                    onChange={() =>
                      toggleValue("skills", item.toLowerCase(), skills)
                    }
                  />
                  {item}
                </label>
              ))}
            </div>
          </div>

          <div>
            <label className="pref-label">Interests</label>
            <div className="checkbox-group">
              {[
                "Technology & Innovation",
                "Arts & Culture",
                "Science & Research",
                "Business & Entrepreneurship",
                "Health & Wellness",
              ].map((item) => (
                <label key={item} className="checkbox-label">
                  <input
                    type="checkbox"
                    checked={interests.includes(item.toLowerCase())}
                    onChange={() =>
                      toggleValue("interests", item.toLowerCase(), interests)
                    }
                  />
                  {item}
                </label>
              ))}
            </div>
          </div>

          <div>
            <label className="pref-label">Values</label>
            <div className="checkbox-group">
              {[
                "Honesty",
                "Cooperation",
                "Innovation",
                "Integrity",
                "Growth",
                "Integrity & Ethics",
                "Collaboration & Teamwork",
                "Growth & Learning",
                "Impact & Contribution",
                "Well-being & Balance",
              ].map((item) => (
                <label key={item} className="checkbox-label">
                  <input
                    type="checkbox"
                    checked={values.includes(item.toLowerCase())}
                    onChange={() =>
                      toggleValue("values", item.toLowerCase(), values)
                    }
                  />
                  {item}
                </label>
              ))}
            </div>
          </div>

          <div>
            <label className="pref-label">Career Preferences</label>
            <div className="checkbox-group">
              {[
                "Software Engineering",
                "Artist",
                "Doctor",
                "Teacher",
                "Data Scientist",
                "Technology & IT",
                "Healthcare & Medicine",
                "Education & Academia",
                "Business & Finance",
                "Arts & Design",
              ].map((item) => (
                <label key={item} className="checkbox-label">
                  <input
                    type="checkbox"
                    checked={careers.includes(item.toLowerCase())}
                    onChange={() =>
                      toggleValue("careers", item.toLowerCase(), careers)
                    }
                  />
                  {item}
                </label>
              ))}
            </div>
          </div>

          {errorMessage && (
            <strong className="auth-error">{errorMessage}</strong>
          )}

          <Button
            text={loading ? "Saving..." : "Begin Journey"}
            className="primary-button pref-button"
            disabled={loading}
            onClickListener={recommendCareers}
          />
        </form>
      </div>
    </div>
  );
};

export default Preferences;
