const Input = ({
  name,
  type,
  required,
  hint,
  value,
  onChangeListener,
  className,
  minLength,
  maxLength,
}) => {
  return (
    <input
      name={name}
      type={type}
      required={required}
      placeholder={hint}
      value={value}
      onChange={onChangeListener}
      className={className}
      minLength={minLength}
      maxLength={maxLength}
    />
  );
};

export default Input;
