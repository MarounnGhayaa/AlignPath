import { createSlice } from "@reduxjs/toolkit";

const initialState = {
    username: "",
    email: "",
    password: "",
    role: "",
    errorMessage: ""
};

const RegisterSlice = createSlice({
    name: "register",
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
        setRole: (state, action) => {
            state.role = action.payload;
        },
        setErrorMessage: (state, action) => {
            state.errorMessage = action.payload;
        },
        clearFields: (state) => {
            state.username = "";
            state.email = "";
            state.password = "";
            state.role = "";
            state.errorMessage = "";
        }
    }
});

export const {
    setField,
    setUsername,
    setEmail,
    setPassword,
    setRole,
    setErrorMessage,
    clearFields
} = RegisterSlice.actions;

export const registerReducer = RegisterSlice.reducer;