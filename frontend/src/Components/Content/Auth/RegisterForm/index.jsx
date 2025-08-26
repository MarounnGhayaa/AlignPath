import "../../Auth/style.css";
import Button from "../../../Button";
import Input from "../../../Input";
import { useNavigate } from "react-router-dom";
import { useRegisterForm } from "./logic.js";

const RegisterForm = ({ toggle }) => {
  const navigate = useNavigate();

  const {
    username,
    setUsername,
    email,
    setEmail,
    password,
    setPassword,
    role,
    setRole,
    errorMessage,
    registerUser,
  } = useRegisterForm();

  return (
    <div className="auth-body">
      <div className="auth-container">
        <h1 className="auth-h1">
          <Button
            text={"←"}
            className="left-button"
            onClickListener={() => {
              navigate("/");
            }}
          />
          <span>AlignPath</span>
        </h1>

        <form className="auth-form" onSubmit={registerUser}>
          <div>
            <label htmlFor="name" className="auth-label">
              Username
            </label>
            <Input
              type={"text"}
              name={"username"}
              hint={"Example"}
              required={true}
              className={"input-style"}
              minLength={3}
              maxLength={30}
              value={username}
              onChangeListener={(e) => setUsername(e.target.value)}
            />
          </div>

          <div>
            <label htmlFor="email" className="auth-label">
              Email
            </label>
            <Input
              type={"text"}
              name={"email"}
              hint={"email@example.com"}
              required={true}
              className={"input-style"}
              minLength={5}
              maxLength={100}
              value={email}
              onChangeListener={(e) => setEmail(e.target.value)}
            />
          </div>

          <div>
            <label htmlFor="password" className="auth-label">
              Password
            </label>
            <Input
              type={"password"}
              name={"password"}
              hint={"************"}
              required={true}
              className={"input-style"}
              minLength={8}
              maxLength={128}
              value={password}
              onChangeListener={(e) => setPassword(e.target.value)}
            />
          </div>
          <section className="auth-radio-row">
            <label className="auth-radio">
              <input
                type="radio"
                name="role"
                value="student"
                checked={role === "student"}
                onChange={() => setRole("student")}
              />
              <strong>Student</strong>
            </label>

            <label className="auth-radio">
              <input
                type="radio"
                name="role"
                value="mentor"
                checked={role === "mentor"}
                onChange={() => setRole("mentor")}
              />
              <strong>Mentor</strong>
            </label>
          </section>

          {errorMessage && <p className="auth-error">{errorMessage}</p>}

          <Button
            text={"Signup"}
            className="primary-button auth-button"
            onClickListener={registerUser}
          />
        </form>

        <strong className="auth-link">
          Already have an account?
          <span className="auth-link-span" onClick={toggle}>
            {" "}
            Login Here
          </span>
        </strong>
      </div>
    </div>
  );
};

export default RegisterForm;
