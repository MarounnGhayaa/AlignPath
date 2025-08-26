import { useState } from "react";
import { useNavigate } from "react-router-dom";
import API from "../../../../Services/axios";

export const useLoginForm = () => {
  const navigate = useNavigate();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [errorMessage, setErrorMessage] = useState("");

  const loginUser = async (e) => {
    e.preventDefault();
    setErrorMessage("");

    try {
      const response = await API.post("/guest/login", {
        email,
        password,
      });

      const token = response.data.payload.token;
      const user = response.data.payload;

      localStorage.setItem("token", token);
      localStorage.setItem("user", JSON.stringify(user));

      navigate("/home");
      return { success: true, user };
    } catch (error) {
      const message = error.response?.data?.message || "Login failed";
      setErrorMessage(message);
      return { success: false, message };
    }
  };

  return {
    email,
    setEmail,
    password,
    setPassword,
    errorMessage,
    loginUser,
  };
};