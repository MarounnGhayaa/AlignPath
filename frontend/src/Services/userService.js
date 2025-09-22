export const readMe = () => {
  try {
    const raw = localStorage.getItem("user") || localStorage.getItem("currentUser");
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
};
