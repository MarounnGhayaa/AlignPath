import "./style.css";
const Button = ({ text, onClickListener, className, insiders, disabled }) => {
  return (
    <button onClick={onClickListener} className={className} disabled={disabled} aria-busy={disabled ? "true" : undefined}>
      {insiders}
      {text}
    </button>
  );
};

export default Button;
