import { createSlice } from "@reduxjs/toolkit";

const initialState = {
    username: "",
    email: "",
    password: "",
    role: "",
    errorMessage: "",
    user: null,
    token: null
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
        },
        setUser: (state, action) => {
            state.user = action.payload;
        },
        setToken: (state, action) => {
            state.token = action.payload;
        },
        clearAuth: (state) => {
            state.user = null;
            state.token = null;
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
    clearFields,
    setUser,
    setToken,
    clearAuth
} = RegisterSlice.actions;

export const registerReducer = RegisterSlice.reducer;