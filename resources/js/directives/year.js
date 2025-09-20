import { useUserStore } from "../pinia/user";

export const checkYear = () => {
  const { fullyear } = useUserStore();
  return fullyear !== new Date().getFullYear();
};

export const YearDirective = {
  mounted(el) {
    if (checkYear()) {
      el.style.display = "none";
    }
  },
};
