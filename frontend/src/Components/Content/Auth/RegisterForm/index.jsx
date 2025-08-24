import "../../Auth/style.css";
import Button from "../../../Button";
import Input from "../../../Input";
import { useNavigate } from "react-router-dom";

const RegisterForm = ({ toggle }) => {
  const navigate = useNavigate();

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

        <form className="auth-form" onSubmit={(e) => e.preventDefault()}>
          <div>
            <label htmlFor="name" className="auth-label">
              Name
            </label>
            <Input
              type={"text"}
              name={"name"}
              hint={"Example"}
              required={true}
              className={"input-style"}
              minLength={3}
              maxLength={30}
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
            />
          </div>

          <Button text={"Signup"} className="primary-button auth-button" />
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
