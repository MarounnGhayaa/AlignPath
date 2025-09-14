export const parseDate = (v) => {
  if (!v) return null;
  const d = new Date(v);
  return isNaN(d.getTime()) ? null : d;
};

export const formatTime = (dateLike, locale) => {
  const d = dateLike instanceof Date ? dateLike : parseDate(dateLike) || new Date();
  return new Intl.DateTimeFormat(locale, { hour: "2-digit", minute: "2-digit" }).format(d);
};
