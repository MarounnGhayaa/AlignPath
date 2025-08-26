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

const MyRoutes = () => {
  return (
    <Routes>
      <Route path="/" element={<Landing />} />
      <Route path="/auth" element={<Auth />} />
      <Route element={<MainLayout />}>
        <Route
          path="/home"
          element={
            <ProtectedRoute allowedRoles={["student", "mentor"]}>
              <Home />
            </ProtectedRoute>
          }
        />
        <Route
          path="/explore"
          element={
            <ProtectedRoute allowedRoles={["student", "mentor"]}>
              <Explore />
            </ProtectedRoute>
          }
        />
        <Route
          path="/network"
          element={
            <ProtectedRoute allowedRoles={["student", "mentor"]}>
              <Network />
            </ProtectedRoute>
          }
        />
        <Route
          path="/path"
          element={
            <ProtectedRoute allowedRoles={["student", "mentor"]}>
              <Path />
            </ProtectedRoute>
          }
        />
        <Route
          path="/profile"
          element={
            <ProtectedRoute allowedRoles={["student", "mentor"]}>
              <Profile />
            </ProtectedRoute>
          }
        />
        <Route
          path="/pathNested"
          element={
            <ProtectedRoute allowedRoles={["student", "mentor"]}>
              <PathNested />
            </ProtectedRoute>
          }
        />
        <Route
          path="/chat"
          element={
            <ProtectedRoute allowedRoles={["student", "mentor"]}>
              <Chat />
            </ProtectedRoute>
          }
        />
      </Route>
    </Routes>
  );
};

export default MyRoutes;
