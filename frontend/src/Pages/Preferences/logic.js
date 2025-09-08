import { useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { useNavigate } from "react-router-dom";
import API from "../../Services/axios";
import {
  setField,
  setErrorMessage,
  clearFields,
} from "../../Features/Preferences/preferencesSlice";

export const usePreferencesLogic = () => {
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const { skills, interests, values, careers, errorMessage } = useSelector(
    (state) => state.preferences
  );

  const [loading, setLoading] = useState(false);

  const registerState = useSelector((state) => state.register) || {};
  const user = registerState.user || JSON.parse(localStorage.getItem("user") || "null");
  const token = registerState.token || localStorage.getItem("token");

  const handleFieldChange = (field, value) => {
    dispatch(setField({ field, value }));
  };

  const handleSavePreferences = async () => {
    try {
      if (!user?.id || !token) {
        dispatch(setErrorMessage("Unauthorized. Please log in again."));
        return;
      }

      setLoading(true);

      await API.post(
        `/user/preferences`,
            {
                skills: skills.join(","),
                interests: interests.join(","),
                values: values.join(","),
                careers: careers.join(","),
            },
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            }
        );

      dispatch(setErrorMessage(""));
      dispatch(clearFields());

      await recommendCareers();

      navigate("/home");
    } catch (error) {
      console.error("Error saving preferences:", error);
      dispatch(setErrorMessage("Failed to save preferences."));
    } finally {
      setLoading(false);
    }
  };

  const handleClear = () => {
    dispatch(clearFields());
  };

  const recommendCareers = async () => {
    try {
       await API.post(
        `/user/recommend-careers`,
        {},
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      );
      } catch (error) {
        console.error("Error fetching recommendations:", error);
      }
  };

  return {
    skills,
    interests,
    values,
    careers,
    errorMessage,
    loading,
    handleFieldChange,
    handleSavePreferences,
    handleClear,
    recommendCareers
  };
};
