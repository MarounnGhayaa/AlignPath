import "../Styles/App.css";
import { Routes, Route } from "react-router-dom";
import Landing from "../Pages/Landing";
import Auth from "../Pages/Auth";
import MainLayout from "../Layouts/MainLayout";
import Home from "../Pages/Home";

const MyRoutes = () => {
  return (
    <Routes>
      <Route path="/" element={<Landing />} />
      <Route path="/auth" element={<Auth />} />
      <Route element={<MainLayout />}>
        <Route path="/home" element={<Home />} />
      </Route>
    </Routes>
  );
};

export default MyRoutes;
