import { useState } from "react";
import { useNavigate } from "react-router-dom";
import API from "../../../../Services/axios";

export const useRegisterForm = () => {
  const navigate = useNavigate();
  const [username, setUsername] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [role, setRole] = useState("student");
  const [errorMessage, setErrorMessage] = useState("");

  const registerUser = async (e) => {
    e.preventDefault();
    setErrorMessage("");

    try {
      const response = await API.post("/guest/register", {
        username,
        email,
        password,
        role,
      });

      const token = response.data.payload.token;
      const user = response.data.payload;

      localStorage.setItem("token", token);
      localStorage.setItem("user", JSON.stringify(user));

      navigate("/home");
    } catch (error) {
      if (error.response) {
        setErrorMessage(error.response.data.message || "Registration failed");
      } else {
        setErrorMessage("Something went wrong. Please try again.");
      }
    }
  };

  return {
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
  };
};
