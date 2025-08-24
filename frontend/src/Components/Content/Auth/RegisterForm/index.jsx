import "../../Auth/style.css";
import Button from "../../../Button";
import Input from "../../../Input";

const RegisterForm = ({ toggle }) => {
  return (
    <div className="auth-body">
      <div className="auth-container">
        <h1 className="auth-h1">
          <Button text={"←"} className="left-button" />
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
