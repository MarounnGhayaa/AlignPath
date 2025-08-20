import "../Styles/App.css";
import { Routes, Route } from "react-router-dom";
import Landing from "../Pages/Landing";
import Auth from "../Pages/Auth";
import MainLayout from "../Layouts/MainLayout";
import Home from "../Pages/Home";
import Explore from "../Pages/Explore";

const MyRoutes = () => {
  return (
    <Routes>
      <Route path="/" element={<Landing />} />
      <Route path="/auth" element={<Auth />} />
      <Route element={<MainLayout />}>
        <Route path="/home" element={<Home />} />
        <Route path="explore" element={<Explore />} />
      </Route>
    </Routes>
  );
};

export default MyRoutes;
