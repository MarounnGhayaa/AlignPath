import "./style.css";
import SubNavBar from "../../Components/SubNavBar";
import Button from "../../Components/Button";
import { useNavigate, useLocation } from "react-router-dom";
import FloatingChatbot from "../../Components/FloatingChatbot";
import { useEffect, useState } from "react";
import { useSelector } from "react-redux";
import API from "../../Services/axios";

const PathNested = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const { pathId, title: passedTitle, initialTab } = location.state || {};

  const registerState = useSelector((state) => state.register) || {};
  const token = registerState.token || localStorage.getItem("token");

  const [title, setTitle] = useState(passedTitle || "");

  useEffect(() => {
    if (title || !pathId || !token) return;
    let cancelled = false;
    const run = async () => {
      try {
        const res = await API.get(`/user/paths`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        const paths = Array.isArray(res.data) ? res.data : [];
        const match = paths.find((p) => String(p.id) === String(pathId));
        if (!cancelled && match?.title) setTitle(match.title);
      } catch {
        // Silently ignore and keep fallback
      }
    };
    run();
    return () => {
      cancelled = true;
    };
  }, [title, pathId, token]);

  return (
    <div className="pathNested-body">
      <h1>
        <Button
          insiders={"←  "}
          text={title || "Title"}
          className={"pathNested-left-button"}
          onClickListener={() => {
            navigate("/path");
          }}
        />
      </h1>
      <SubNavBar pathId={pathId} initialTab={initialTab} />
      <FloatingChatbot />
    </div>
  );
};

export default PathNested;
