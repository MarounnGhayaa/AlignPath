import { createSlice, createAsyncThunk } from "@reduxjs/toolkit";
import API from "../../Services/axios";

export const fetchSkills = createAsyncThunk(
  "skills/fetchByPath",
  async (pathId, { rejectWithValue }) => {
    try {
      const res = await API.get(`/user/skills/${pathId}`);
      return { pathId, skills: res.data };
    } catch (err) {
      return rejectWithValue({ pathId, message: err?.response?.data || "Failed to fetch skills" });
    }
  }
);

export const persistSkills = createAsyncThunk(
  "skills/persistByPath",
  async (pathId, { getState, rejectWithValue }) => {
    try {
      const state = getState();
      const items = state.skills?.byPathId?.[pathId]?.items || [];
      // Persist each skill value
      await Promise.all(
        items.map((s) => API.put(`/user/skills/${s.id}`, { value: s.value }))
      );
      return { pathId };
    } catch (err) {
      return rejectWithValue({ pathId, message: err?.response?.data || "Failed to persist skills" });
    }
  }
);

export const incrementAndPersist = createAsyncThunk(
  "skills/incrementAndPersist",
  async ({ pathId, percent }, { dispatch, getState }) => {
    // Ensure skills are loaded
    const state = getState();
    const hasItems = state.skills?.byPathId?.[pathId]?.items?.length > 0;
    if (!hasItems) {
      await dispatch(fetchSkills(pathId));
    }
    // Update locally, then persist
    dispatch(incrementAll({ pathId, percent }));
    await dispatch(persistSkills(pathId));
    return { pathId };
  }
);

const initialState = {
  byPathId: {}
};

const clamp = (val, min, max) => Math.max(min, Math.min(max, val));

const skillsSlice = createSlice({
  name: "skills",
  initialState,
  reducers: {
    setSkillsForPath: (state, action) => {
      const { pathId, skills } = action.payload;
      state.byPathId[pathId] = {
        items: skills,
        loading: false,
        error: null,
      };
    },
    incrementAll: (state, action) => {
      const { pathId, percent } = action.payload;
      const entry = state.byPathId[pathId];
      if (!entry) return;
      entry.items = entry.items.map((s) => ({
        ...s,
        value: clamp(Math.round(s.value + percent), 0, 100),
      }));
    }
  },
  extraReducers: (builder) => {
    builder
      .addCase(fetchSkills.pending, (state, action) => {
        const pathId = action.meta.arg;
        state.byPathId[pathId] = state.byPathId[pathId] || { items: [], loading: false, error: null };
        state.byPathId[pathId].loading = true;
        state.byPathId[pathId].error = null;
      })
      .addCase(fetchSkills.fulfilled, (state, action) => {
        const { pathId, skills } = action.payload;
        state.byPathId[pathId] = {
          items: skills,
          loading: false,
          error: null,
        };
      })
      .addCase(fetchSkills.rejected, (state, action) => {
        const { pathId, message } = action.payload || {};
        if (pathId == null) return;
        state.byPathId[pathId] = state.byPathId[pathId] || { items: [], loading: false, error: null };
        state.byPathId[pathId].loading = false;
        state.byPathId[pathId].error = message || "Failed to fetch skills";
      });
  }
});

export const { setSkillsForPath, incrementAll } = skillsSlice.actions;
export const skillsReducer = skillsSlice.reducer;
