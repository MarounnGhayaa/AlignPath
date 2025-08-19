const Button = ({ text, onClickListener, className, insiders }) => {
  return (
    <button onClick={onClickListener} className={className}>
      {insiders}
      {text}
    </button>
  );
};

export default Button;
