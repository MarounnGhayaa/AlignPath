import { useNavigate } from "react-router-dom";
import API from "../../../../Services/axios";
import { useSelector, useDispatch } from "react-redux";
import { setField, setUsername, setEmail, setPassword, setRole, setErrorMessage, clearFields, setUser, setToken } from "../../../../Features/Register/registerSlice";

export const useRegisterForm = () => {
  const navigate = useNavigate();
  const dispatch = useDispatch();
  const { username, email, password, role, errorMessage } = useSelector((global) => global.register);

  const registerUser = async (e) => {
    e.preventDefault();
    dispatch(setErrorMessage(""));

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

      dispatch(setUser(user));
      dispatch(setToken(token));

      dispatch(clearFields());

      navigate("/preferences");
    } catch (error) {
      if (error.response) {
        dispatch(setErrorMessage(error.response.data.message || "Registration failed"));
      } else {
        dispatch(setErrorMessage("Something went wrong. Please try again."));
      }
    }
  };

    const handleFieldChange = (field, value) => {
      dispatch(setField({ field, value }));
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
    handleFieldChange,
    registerUser,
  };
};
