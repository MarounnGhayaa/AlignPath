import { configureStore } from "@reduxjs/toolkit";
import { loginReducer } from "../Features/Login/loginSlice.js";

export const store = configureStore({
  reducer: {
    login: loginReducer
  }
});