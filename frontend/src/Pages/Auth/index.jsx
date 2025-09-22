import { useState } from "react";
import LoginForm from "../../Components/Content/Auth/LoginForm";
import RegisterForm from "../../Components/Content/Auth/RegisterForm";

const Auth = () => {
  const [isLogin, setIsLogin] = useState(true);

  const switchForm = () => {
    setIsLogin(!isLogin);
  };

  return (
    <div className="auth-page">
      <div className="auth-box">
        {isLogin ? (
          <LoginForm toggle={switchForm} />
        ) : (
          <RegisterForm toggle={switchForm} />
        )}
      </div>
    </div>
  );
};

export default Auth;
