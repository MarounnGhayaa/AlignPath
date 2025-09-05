import { createSlice } from "@reduxjs/toolkit";

const initialState = {
  username: "",
  email: "",
  password: "",
  role: "",
  location: "",
  errorMessage: "",
  successMessage: "",
};

const ProfileSlice = createSlice({
  name: "profile",
  initialState,
  reducers: {
    setField: (state, action) => {
      const { field, value } = action.payload;
      state[field] = value;
    },
    setUsername: (state, action) => {
      state.username = action.payload;
    },
    setEmail: (state, action) => {
      state.email = action.payload;
    },
    setPassword: (state, action) => {
      state.password = action.payload;
    },
    setLocation: (state, action) => {
      state.location = action.payload;
    },
    setErrorMessage: (state, action) => {
      state.errorMessage = action.payload;
    },
    setSuccessMessage: (state, action) => {
      state.successMessage = action.payload;
    },
    clearMessages: (state) => {
      state.errorMessage = "";
      state.successMessage = "";
    },
    resetForm: (state) => {
      state.username = "";
      state.email = "";
      state.password = "";
      state.location = "";
    },
    setAllFields: (state, action) => {
      const user = action.payload;

      state.username = user.username || "";
      state.email = user.email || "";
      state.location = user.location || "";
      state.role = user.role || "";
    },
  },
});

export const {
  setField,
  setUsername,
  setEmail,
  setPassword,
  setLocation,
  setErrorMessage,
  setSuccessMessage,
  clearMessages,
  resetForm,
  setAllFields,
} = ProfileSlice.actions;

export const profileReducer = ProfileSlice.reducer;

