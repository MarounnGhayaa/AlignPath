import "../Styles/App.css";
import { Routes, Route } from "react-router-dom";
import Landing from "../Pages/Landing";
import Auth from "../Pages/Auth";
import MainLayout from "../Layouts/MainLayout";
import Home from "../Pages/Home";
import Explore from "../Pages/Explore";
import Network from "../Pages/Network";
import Path from "../Pages/Path";
import Profile from "../Pages/Profile";
import PathNested from "../Pages/PathNested";

const MyRoutes = () => {
  return (
    <Routes>
      <Route path="/" element={<Landing />} />
      <Route path="/auth" element={<Auth />} />
      <Route element={<MainLayout />}>
        <Route path="/home" element={<Home />} />
        <Route path="/explore" element={<Explore />} />
        <Route path="/network" element={<Network />} />
        <Route path="/path" element={<Path />} />
        <Route path="/profile" element={<Profile />} />
        <Route path="/pathNested" element={<PathNested />} />
      </Route>
    </Routes>
  );
};

export default MyRoutes;
