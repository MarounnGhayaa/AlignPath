import "../../Auth/style.css";
import Button from "../../../Button";
import Input from "../../../Input";

const LoginForm = ({ toggle }) => {
  return (
    <div className="auth-body">
      <div className="auth-container">
        <h1 className="auth-h1">
          <Button text={"←"} className="left-button" />
          <span>AlignPath</span>
        </h1>

        <form className="auth-form" onSubmit={(e) => e.preventDefault()}>
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
            />
          </div>

          <Button text={"Login"} className="primary-button auth-button" />
        </form>

        <strong className="auth-link">
          Don't have an account?
          <span className="auth-link-span" onClick={toggle}>
            {" "}
            Sign Up Here
          </span>
        </strong>
      </div>
    </div>
  );
};

export default LoginForm;
