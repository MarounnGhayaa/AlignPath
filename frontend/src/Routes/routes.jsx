import "../Styles/App.css";
import { Routes, Route } from "react-router-dom";
import Landing from "../Pages/Landing";

const MyRoutes = () => {
  return (
    <Routes>
      <Route path="/" element={<Landing />} />
    </Routes>
  );
};

export default MyRoutes;
