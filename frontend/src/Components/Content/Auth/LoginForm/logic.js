import { useNavigate } from "react-router-dom";
import API from "../../../../Services/axios";
import { useSelector, useDispatch } from "react-redux";
import { setField, setErrorMessage, clearFields } from "../../../../Features/Login/loginSlice";

export const useLoginForm = () => {
  const navigate = useNavigate();
  const dispatch = useDispatch();
  const { email, password, errorMessage } = useSelector((global) => global.login);

  const loginUser = async (e) => {
    e.preventDefault();
    dispatch(setErrorMessage(""));

    try {
      const res = await API.post("/guest/login", { email, password });

      const payload = res?.data?.payload || {};
      const token   = payload.token;
      const user    = payload.user ?? payload;

      if (!token || !user) throw new Error("Malformed login response");

      localStorage.setItem("token", token);
      localStorage.setItem("user", JSON.stringify(user));

      dispatch(clearFields());

      const role = String(user?.role || "").toLowerCase();
      if (role === "admin") {
        navigate("/adminDashboard");
      } else {
        navigate("/home");
      }
    } catch (err) {
      const msg = err?.response?.data?.message || "Incorrect Email or Password";
      dispatch(setErrorMessage(msg));
    }
  };

  const handleFieldChange = (field, value) => {
    dispatch(setField({ field, value }));
  };

  return { email, password, errorMessage, handleFieldChange, loginUser };
};
