import "./style.css";
import { User } from "lucide-react";
import Input from "../../Components/Input";
import Avatar from "../../Assets/Avatar.png";
import Button from "../../Components/Button";
import { useProfileLogic } from "./logic";

const Profile = () => {
  const {
    username,
    email,
    password,
    location,
    errorMessage,
    successMessage,
    handleFieldChange,
    handleSaveChanges,
  } = useProfileLogic();

  return (
    <div className="profile-body">
      <h1>My Profile</h1>

      <div className="profile-main">
        <div className="profile-user-info">
          <div className="profile-user-header">
            <div className="profile-user-img">
              <span>
                <User />
              </span>
            </div>
            <div className="profile-user-titles">
              <h3>{username}</h3>
              <strong>{email}</strong>
            </div>
          </div>

          <div className="profile-user-field-section">
            <div className="profile-user-fields">
              <label htmlFor="username">
                <strong>Username</strong>
              </label>
              <Input
                name="username"
                type="text"
                required
                hint="username"
                minLength={3}
                maxLength={30}
                value={username}
                onChangeListener={(e) =>
                  handleFieldChange("username", e.target.value)
                }
              />

              <label htmlFor="email">
                <strong>Email</strong>
              </label>
              <Input
                name="email"
                type="email"
                required
                hint="username@email.com"
                minLength={5}
                maxLength={100}
                value={email}
                onChangeListener={(e) =>
                  handleFieldChange("email", e.target.value)
                }
              />

              <label htmlFor="password">
                <strong>Password</strong>
              </label>
              <Input
                name="password"
                type="password"
                required
                hint="*************"
                minLength={8}
                maxLength={128}
                value={password}
                onChangeListener={(e) =>
                  handleFieldChange("password", e.target.value)
                }
              />

              <label htmlFor="location">
                <strong>Location</strong>
              </label>
              <Input
                name="location"
                type="text"
                required
                hint="Kobayat, Lebanon"
                minLength={2}
                maxLength={100}
                value={location}
                onChangeListener={(e) =>
                  handleFieldChange("location", e.target.value)
                }
              />
            </div>

            <div className="profile-avatar-img">
              <img
                src={Avatar}
                alt="AlignPath Avatar"
                className="profile-avatar"
              />
            </div>
          </div>
        </div>

        <div className="profile-side-box">
          <div className="profile-side-sections">
            <div className="profile-side-section">
              <h2>Profile Picture</h2>
              <div className="profile-user-upload-img">
                <span>
                  <User />
                </span>
              </div>
              <Button className="primary-button" text="Upload Picture" />
            </div>

            <div className="profile-side-section">
              <div className="profile-user-save-changes">
                <h2>About</h2>
                <h4>
                  {username
                    ? `${username}, based in ${
                        location || "Somewhere on earth"
                      }; planning to build a successful career!`
                    : "Student name, based in location; planning to build a successful career!"}
                </h4>

                <Button
                  className="primary-button"
                  text="Save Changes"
                  onClickListener={handleSaveChanges}
                />

                {errorMessage && (
                  <p className="profile-error-message">
                    <strong>{errorMessage}</strong>
                  </p>
                )}
                {successMessage && (
                  <p className="profile-success-message">
                    <strong>{successMessage}</strong>
                  </p>
                )}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Profile;
