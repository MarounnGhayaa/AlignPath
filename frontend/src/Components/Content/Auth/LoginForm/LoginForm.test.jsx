import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import LoginForm from "./index.jsx";
import { useLoginForm } from "./logic.js";

jest.mock("./logic.js", () => ({
  useLoginForm: jest.fn(),
}));

let mockNavigate;
jest.mock(
  "react-router-dom",
  () => ({
    useNavigate: () => mockNavigate,
  }),
  { virtual: true }
);

describe("LoginForm", () => {
  let loginUser, handleFieldChange;

  beforeEach(() => {
    mockNavigate = jest.fn();
    loginUser = jest.fn((e) => e?.preventDefault && e.preventDefault());
    handleFieldChange = jest.fn();

    useLoginForm.mockReturnValue({
      email: "",
      password: "",
      errorMessage: "",
      handleFieldChange,
      loginUser,
    });
  });

  test('clicking "Login" triggers login handler', async () => {
    const user = userEvent.setup();
    render(<LoginForm toggle={() => {}} />);
    await user.click(screen.getByRole("button", { name: /login/i }));
    expect(loginUser).toHaveBeenCalled();
  });

  test("back arrow navigates home", async () => {
    const user = userEvent.setup();
    render(<LoginForm toggle={() => {}} />);
    await user.click(screen.getByRole("button", { name: "←" }));
    expect(mockNavigate).toHaveBeenCalledWith("/");
  });

  test("renders error message from hook", () => {
    useLoginForm.mockReturnValue({
      email: "",
      password: "",
      errorMessage: "Invalid credentials",
      handleFieldChange,
      loginUser,
    });
    render(<LoginForm toggle={() => {}} />);
    expect(screen.getByText("Invalid credentials")).toBeInTheDocument();
  });

  test('clicking "Signup" calls toggle', async () => {
    const user = userEvent.setup();
    const toggle = jest.fn();
    render(<LoginForm toggle={toggle} />);
    await user.click(screen.getByText("Signup"));
    expect(toggle).toHaveBeenCalled();
  });
});
