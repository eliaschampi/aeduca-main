import { defineStore } from "pinia";
import cache from "../core/cache";
import { useRefreshStore } from "./refresh";
import { useSnackbarStore } from "./snackbar";

export const useResponseStore = defineStore("response", {
  state: () => ({
    reauthenticate: "Token has expired",
    black: ["Token Signature could not be verified.", "The token has been blacklisted"],
    refresh_code: 401,
  }),

  actions: {
    handleResponseError({ message, code }) {
      if (code === 401) {
        if (message === this.reauthenticate) {
          const { showRefresh } = useRefreshStore();
          showRefresh("Se requiere autenticación", "/auth/refresh");
          return;
        }
        if (this.black.indexOf(message) !== -1) {
          cache.cleanAll();
          location.reload();
        }
      }
      const { dangerSnack } = useSnackbarStore();
      dangerSnack(message);
    },
  },
});
