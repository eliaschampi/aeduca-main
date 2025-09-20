import { defineStore } from "pinia";
import { ref } from "vue";
import cache from "../core/cache";

export const useThemeStore = defineStore("theme", () => {
  const theme = ref(cache.getItem("theme") || "light");

  function storeTheme(value) {
    theme.value = value;
    cache.setItem("theme", value);
  }

  function systemTheme() {
    return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
  }

  function preferredTheme() {
    if (theme.value) {
      return theme.value;
    }
    return systemTheme();
  }

  function setTheme(value) {
    document.documentElement.setAttribute("data-bs-theme", value);
  }

  function changeTheme(value) {
    setTheme(value);
    storeTheme(value);
  }

  function systemThemeListener() {
    window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", () => {
      const sTheme = systemTheme();
      changeTheme(sTheme);
    });
  }

  setTheme(preferredTheme());
  systemThemeListener();

  return {
    change: changeTheme,
    theme,
  };
});
