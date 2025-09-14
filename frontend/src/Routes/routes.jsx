import "../Styles/App.css";
import { Routes, Route } from "react-router-dom";

import MainLayout from "../Layouts/MainLayout";
import ProtectedRoute from "../Layouts/ProtectedRoutes";

import Landing from "../Pages/Landing";
import Auth from "../Pages/Auth";
import Home from "../Pages/Home";
import Explore from "../Pages/Explore";
import Network from "../Pages/Network";
import Path from "../Pages/Path";
import Profile from "../Pages/Profile";
import PathNested from "../Pages/PathNested";
import Chat from "../Pages/Chat";
import SolveQuest from "../Pages/SolveQuest";
import SolveProblem from "../Pages/SolveProblem";
import Preferences from "../Pages/Preferences";
import AdminDashboard from "../Pages/Admin";

const MyRoutes = () => {
  return (
    <Routes>
      <Route path="/" element={<Landing />} />
      <Route path="/auth" element={<Auth />} />
      <Route
        path="/preferences"
        element={
          <ProtectedRoute allowedRoles={["student", "mentor", "admin"]}>
            <Preferences />
          </ProtectedRoute>
        }
      />
      <Route element={<MainLayout />}>
        <Route
          path="/home"
          element={
            <ProtectedRoute allowedRoles={["student", "mentor", "admin"]}>
              <Home />
            </ProtectedRoute>
          }
        />
        <Route
          path="/explore"
          element={
            <ProtectedRoute allowedRoles={["student", "mentor", "admin"]}>
              <Explore />
            </ProtectedRoute>
          }
        />
        <Route
          path="/network"
          element={
            <ProtectedRoute allowedRoles={["student", "mentor", "admin"]}>
              <Network />
            </ProtectedRoute>
          }
        />
        <Route
          path="/path"
          element={
            <ProtectedRoute allowedRoles={["student", "mentor", "admin"]}>
              <Path />
            </ProtectedRoute>
          }
        />
        <Route
          path="/profile"
          element={
            <ProtectedRoute allowedRoles={["student", "mentor", "admin"]}>
              <Profile />
            </ProtectedRoute>
          }
        />
        <Route
          path="/pathNested"
          element={
            <ProtectedRoute allowedRoles={["student", "mentor", "admin"]}>
              <PathNested />
            </ProtectedRoute>
          }
        />
        <Route
          path="/chat"
          element={
            <ProtectedRoute allowedRoles={["student", "mentor", "admin"]}>
              <Chat />
            </ProtectedRoute>
          }
        />
        <Route
          path="/solveQuest"
          element={
            <ProtectedRoute allowedRoles={["student", "mentor", "admin"]}>
              <SolveQuest />
            </ProtectedRoute>
          }
        />
        <Route
          path="/solveProblem/:problemId"
          element={
            <ProtectedRoute allowedRoles={["student", "mentor", "admin"]}>
              <SolveProblem />
            </ProtectedRoute>
          }
        />
      </Route>
      <Route
        path="/adminDashboard"
        element={
          <ProtectedRoute allowedRoles={["admin"]}>
            <AdminDashboard />
          </ProtectedRoute>
        }
      />
    </Routes>
  );
};

export default MyRoutes;
