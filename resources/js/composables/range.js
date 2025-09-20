export const useRangeDetail = (from, to) => {
  const fromDate = new Date(from);
  const toDate = new Date(to);
  const now = new Date();

  const total = Math.abs(toDate - fromDate) / (1000 * 3600 * 24);
  const daysPassed = () => {
    if (now < fromDate) {
      return 0;
    }
    if (now > toDate) {
      return total;
    }
    return parseInt(Math.abs(now - fromDate) / (1000 * 3600 * 24));
  };
  const percentage = Math.min((daysPassed() * 100) / total, 100).toFixed(2);
  return {
    total,
    passed: daysPassed(),
    percentage,
  };
};
