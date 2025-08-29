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
    } catch (error) {
      if (error.response) {
        setErrorMessage(error.response.data.message || "Incorrect Email or Password");
      } else {
        setErrorMessage("Something went wrong. Please try again.");
      }
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