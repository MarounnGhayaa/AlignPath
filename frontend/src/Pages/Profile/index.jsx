import "./style.css";
import { User } from "lucide-react";
import Input from "../../Components/Input";
import WhiteLogo from "../../Assets/LogoWhite.png";

const Profile = () => {
  return (
    <div className="profile-body">
      <h1>My Profile</h1>
      <div className="profile-user-info">
        <div className="profile-user-header">
          <div className="profile-user-img">
            <span>
              <User />
            </span>
          </div>
          <div className="profile-user-titles">
            <h3>Username</h3>
            <p>username@email.com</p>
          </div>
        </div>
        <div className="profile-user-field-section">
          <div className="profile-user-fields">
            <label htmlFor="username">
              <strong>Username</strong>
            </label>
            <Input
              name={"username"}
              type={"text"}
              required={true}
              hint={"username"}
              minLength={2}
              maxLength={50}
            />
            <label htmlFor="email">
              <strong>Email</strong>
            </label>
            <Input
              name={"email"}
              type={"email"}
              required={true}
              hint={"username@email.com"}
              minLength={5}
              maxLength={50}
            />
            <label htmlFor="password">
              <strong>Password</strong>
            </label>
            <Input
              name={"password"}
              type={"password"}
              required={true}
              hint={"*************"}
              minLength={2}
              maxLength={50}
            />
            <label htmlFor="location">
              <strong>Location</strong>
            </label>
            <Input
              name={"location"}
              type={"string"}
              required={true}
              hint={"Kobayat, Lebanon"}
              minLength={2}
              maxLength={50}
            />
          </div>
          <div className="profile-logo-img">
            <img
              src={WhiteLogo}
              alt="AlignPath Logo"
              className="profile-WLogo"
            />
          </div>
        </div>
      </div>
    </div>
  );
};

export default Profile;
