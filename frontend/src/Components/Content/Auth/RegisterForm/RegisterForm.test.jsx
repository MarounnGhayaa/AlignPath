import "@testing-library/jest-dom";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import RegisterForm from "./index.jsx";
import { useRegisterForm } from "./logic.js";

jest.mock("./logic.js", () => ({
  useRegisterForm: jest.fn(),
}));

let mockNavigate;
jest.mock(
  "react-router-dom",
  () => ({
    useNavigate: () => mockNavigate,
  }),
  { virtual: true }
);

const getUser = () => (userEvent.setup ? userEvent.setup() : userEvent);

describe("RegisterForm", () => {
  let registerUser, handleFieldChange;

  beforeEach(() => {
    mockNavigate = jest.fn();
    registerUser = jest.fn((e) => e?.preventDefault && e.preventDefault());
    handleFieldChange = jest.fn();

    useRegisterForm.mockReturnValue({
      username: "",
      email: "",
      password: "",
      role: "student",
      errorMessage: "",
      handleFieldChange,
      registerUser,
    });
  });

  test('clicking "Signup" triggers register handler', async () => {
    const user = getUser();
    render(<RegisterForm toggle={() => {}} />);
    await user.click(screen.getByRole("button", { name: /signup/i }));
    expect(registerUser).toHaveBeenCalled();
  });

  test("role radios reflect state and update via clicks", async () => {
    const user = getUser();
    const { rerender } = render(<RegisterForm toggle={() => {}} />);

    let student = screen.getByRole("radio", { name: /student/i });
    let mentor = screen.getByRole("radio", { name: /mentor/i });

    expect(student).toBeChecked();
    expect(mentor).not.toBeChecked();

    await user.click(mentor);
    expect(handleFieldChange).toHaveBeenNthCalledWith(1, "role", "mentor");

    useRegisterForm.mockReturnValue({
      username: "",
      email: "",
      password: "",
      role: "mentor",
      errorMessage: "",
      handleFieldChange,
      registerUser,
    });
    rerender(<RegisterForm toggle={() => {}} />);

    student = screen.getByRole("radio", { name: /student/i });
    mentor = screen.getByRole("radio", { name: /mentor/i });
    expect(mentor).toBeChecked();
    expect(student).not.toBeChecked();

    await user.click(student);
    expect(handleFieldChange).toHaveBeenNthCalledWith(2, "role", "student");
  });

  test("back arrow navigates home", async () => {
    const user = getUser();
    render(<RegisterForm toggle={() => {}} />);
    await user.click(screen.getByRole("button", { name: "←" }));
    expect(mockNavigate).toHaveBeenCalledWith("/");
  });

  test('clicking "Login" calls toggle', async () => {
    const user = getUser();
    const toggle = jest.fn();
    render(<RegisterForm toggle={toggle} />);
    await user.click(screen.getByText("Login"));
    expect(toggle).toHaveBeenCalled();
  });

  test("renders error message from hook", () => {
    useRegisterForm.mockReturnValue({
      username: "",
      email: "",
      password: "",
      role: "student",
      errorMessage: "Email already in use",
      handleFieldChange,
      registerUser,
    });
    render(<RegisterForm toggle={() => {}} />);
    expect(screen.getByText("Email already in use")).toBeInTheDocument();
  });
});
