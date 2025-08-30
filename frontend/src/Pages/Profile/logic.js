import { useEffect } from "react";
import API from "../../Services/axios";
import { useSelector, useDispatch } from "react-redux";
import {
  setField,
  setAllFields,
  setErrorMessage,
  setSuccessMessage,
  clearMessages
} from "../../Features/Profile/profileSlice";

export const useProfileLogic = () => {
  const dispatch = useDispatch();
  const { username, email, password, location, errorMessage, successMessage } =
    useSelector((state) => state.profile);

  useEffect(() => {
    const token = localStorage.getItem("token");
    const user = JSON.parse(localStorage.getItem("user"));

    if (!user?.id) return;

    const fetchProfile = async () => {
      try {
        const response = await API.get(`/user/getInfo/${user.id}`, {
          headers: { Authorization: `Bearer ${token}` },
        });

        const profile = response.data.payload;
        dispatch(setAllFields(profile));
      } catch (error) {
        console.error("Error fetching profile:", error);
        dispatch(setErrorMessage("Error fetching profile data."));
      }
    };

    fetchProfile();
  }, [dispatch]);

  const handleFieldChange = (field, value) => {
    dispatch(setField({ field, value }));
  };

  const handleSaveChanges = async () => {
    const token = localStorage.getItem("token");
    const user = JSON.parse(localStorage.getItem("user"));

    try {
      const response = await API.put(
        `/user/updateInfo/${user.id}`,
        { username, email, password, location },
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      );

      const updatedProfile = response.data.payload;
      dispatch(setAllFields(updatedProfile));
      dispatch(setSuccessMessage("Profile updated successfully!"));
      setTimeout(() => {
        dispatch(clearMessages());
      }, 3000);

      localStorage.setItem("user", JSON.stringify(updatedProfile));
    } catch (error) {
      console.error("Error saving profile:", error);
      dispatch(
        setErrorMessage(
          error.response?.data?.message || "Error updating profile."
        )
      );
      setTimeout(() => {
        dispatch(clearMessages());
      }, 3000);
    }
  };

  return {
    username,
    email,
    password,
    location,
    errorMessage,
    successMessage,
    handleFieldChange,
    handleSaveChanges,
  };
};
