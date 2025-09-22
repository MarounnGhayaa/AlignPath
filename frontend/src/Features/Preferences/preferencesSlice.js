import { createSlice } from "@reduxjs/toolkit";

const initialState = {
  skills: [],
  interests: [],
  values: [],
  careers: [],
  errorMessage: ""
};

const PreferencesSlice = createSlice({
  name: "preferences",
  initialState,
  reducers: {
    setField: (state, action) => {
      const { field, value } = action.payload;
      state[field] = value;
    },
    setSkills: (state, action) => {
      state.skills = action.payload;
    },
    setInterests: (state, action) => {
      state.interests = action.payload;
    },
    setValues: (state, action) => {
      state.values = action.payload;
    },
    setCareers: (state, action) => {
      state.careers = action.payload;
    },
    setErrorMessage: (state, action) => {
      state.errorMessage = action.payload;
    },
    clearFields: (state) => {
      state.skills = [];
      state.interests = [];
      state.values = [];
      state.careers = [];
      state.errorMessage = "";
    }
  }
});

export const {
  setField,
  setSkills,
  setInterests,
  setValues,
  setCareers,
  setErrorMessage,
  clearFields
} = PreferencesSlice.actions;

export const preferencesReducer = PreferencesSlice.reducer;
