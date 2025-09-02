import { createSlice } from "@reduxjs/toolkit";

const initialState = {
  input: "",
  loading: false,
  messages: [
    { role: "model", content: "Hi! I'm your AI assistant. How can I help?" },
  ],
};

const AiChatSlice = createSlice({
  name: "aiChat",
  initialState,
  reducers: {
    setField: (state, action) => {
      const { field, value } = action.payload;
      state[field] = value;
    },
    setInput: (state, action) => {
      state.input = action.payload;
    },
    setLoading: (state, action) => {
      state.loading = action.payload;
    },
    addMessage: (state, action) => {
      state.messages.push(action.payload);
    },
    setMessages: (state, action) => {
      state.messages = action.payload;
    },
    clearChat: (state) => {
      state.input = "";
      state.loading = false;
      state.messages = [
        { role: "model", content: "Hi! I'm your AI assistant. How can I help?" },
      ];
    },
  },
});

export const {
  setField,
  setInput,
  setLoading,
  addMessage,
  setMessages,
  clearChat,
} = AiChatSlice.actions;

export const aiChatReducer = AiChatSlice.reducer;
