import { defineStore } from "pinia";
import { ref } from "vue";

export const useSnackbarStore = defineStore("snackbar", () => {
  const themes = {
    danger: "danger",
    show: "default",
    success: "success",
    warning: "warning",
  };

  const active = ref(false);
  const text = ref("");
  const theme = ref("default");
  const button = ref("");
  const action = ref(null);
  const timeout = ref(5000);
  let timer;

  const setSnackbarState = (options) => {
    active.value = true;
    text.value = options.text;
    theme.value = options.theme || "default";
    button.value = options.button;
    action.value = options.action;
    clearTimeout(timer);
    timer = setTimeout(() => {
      closeSnack();
    }, timeout.value);
  };

  const showSnack = (options) => {
    setSnackbarState({
      ...options,
      theme: themes[options.theme] || themes.show,
    });
  };

  const successSnack = (text, button, action) => {
    showSnack({ text, button, action, theme: "success" });
  };

  const dangerSnack = (text, button, action) => {
    showSnack({ text, button, action, theme: "danger" });
  };

  const warningSnack = (text, button, action) => {
    showSnack({ text, button, action, theme: "warning" });
  };

  const closeSnack = () => {
    active.value = false;
  };

  return {
    showSnack,
    successSnack,
    dangerSnack,
    warningSnack,
    closeSnack,
    active,
    text,
    theme,
    button,
    action,
  };
});
