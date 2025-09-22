import API from "./axios";

const listEndpoints = (iAmMentor) =>
  iAmMentor ? ["user/users"] : ["user/mentors"];

export async function fetchPeople({ iAmMentor, searchTerm, myId, signal }) {
  const candidates = listEndpoints(iAmMentor);
  let data = [];
  let lastErr = null;

  for (const url of candidates) {
    try {
      const token = localStorage.getItem("token");
      const res = await API.get(url, {
        params: searchTerm ? { search: searchTerm } : undefined,
        headers: token ? { Authorization: `Bearer ${token}` } : undefined,
        signal,
      });
      data = res?.data?.data ?? res?.data ?? [];
      if (Array.isArray(data)) break;
    } catch (e) {
      lastErr = e;
    }
  }

  if (!Array.isArray(data)) {
    const msg = lastErr?.response?.status
      ? `List fetch failed (${lastErr.response.status})`
      : lastErr?.message || "Error loading list";
    throw new Error(msg);
  }

  const normalized = data
    .map((u) => ({
      ...u,
      role: (u.role || (iAmMentor ? "user" : "mentor")).toLowerCase(),
      expertise: Array.isArray(u.expertise) ? u.expertise : [],
    }))
    .filter((u) => (myId ? u.id !== myId : true));

  return normalized;
}
