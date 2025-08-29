import { createSlice } from "@reduxjs/toolkit";

const initialState = {
    email: "",
    password: "",
    errorMessage: ""
};

const LoginSlice = createSlice({
    name: "login",
    initialState,
    reducers: {
        setField: (state, action) => {
            const { field, value } = action.payload;
            state[field] = value;
        },
        setEmail: (state, action) => {
            state.email = action.payload;
        },
        setPassword: (state, action) => {
            state.password = action.payload;
        },
        setErrorMessage: (state, action) => {
            state.errorMessage = action.payload;
        },
    }
});

export const {
    setField,
    setEmail,
    setPassword,
    setErrorMessage
} = LoginSlice.actions;

export const loginReducer = LoginSlice.reducer;