import { configureStore } from "@reduxjs/toolkit";
import { loginReducer } from "../Features/Login/loginSlice.js";
import { registerReducer } from "../Features/Register/registerSlice.js";
import { profileReducer } from "../Features/Profile/profileSlice.js";
import { aiChatReducer } from "../Features/AiChat/AiChatSlice.js";
import { preferencesReducer } from "../Features/Preferences/preferencesSlice.js";
import { skillsReducer } from "../Features/Skill/skillsSlice.js";

export const store = configureStore({
  reducer: {
    login: loginReducer,
    register: registerReducer,
    profile: profileReducer,
    aiChat: aiChatReducer,
    preferences: preferencesReducer,
    skills: skillsReducer,
  }
});
