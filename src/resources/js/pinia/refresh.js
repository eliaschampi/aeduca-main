import { defineStore } from "pinia";
import cache from "../core/cache";
import axios from "../http/axios";
import { useModalStore } from "./modal";
import { useSnackbarStore } from "./snackbar";
import { useUserStore } from "./user";

export const useRefreshStore = defineStore({
    id: "refresh",
    state: () => ({
        password: "",
        title: "",
        RATE_LIMIT: 5,
        counter: 0,
        who: "",
        error: "",
    }),
    actions: {
        incrementCounter() {
            this.counter++;
        },
        resetCounter() {
            this.counter = 0;
        },
        cleanAndExit() {
            cache.cleanAll();
            location.reload();
        },
        showRefresh(title, who) {
            this.title = title;
            this.who = who;
            if (this.counter < 4) {
                const { showModal } = useModalStore();
                showModal("reauth");
            } else if (this.counter < 5) {
                const { warningSnack } = useSnackbarStore();
                warningSnack("Se ha detectado varios intentos de acceso");
                this.cleanAndExit();
            }
        },
        async onCheck(onSuccess) {
            if (!this.password) {
                return;
            }
            try {
                const {
                    user: { email },
                    updateUserCached,
                } = useUserStore();
                const {
                    data: { valid, hasuser, user },
                } = await axios.post(this.who, {
                    email,
                    password: this.password,
                });
                if (valid) {
                    if (hasuser) {
                        updateUserCached(user);
                    } else {
                        onSuccess();
                    }
                    this.error = "";
                    this.resetCounter();
                    const { hideModal } = useModalStore();
                    hideModal("reauth");
                } else {
                    this.error = `Contraseña incorrecta. Te quedan ${this.remaining} opciones`;
                    if (this.counter >= this.RATE_LIMIT) {
                        this.cleanAndExit();
                    }
                    this.incrementCounter();
                }
            } catch {
                this.cleanAndExit();
            } finally {
                this.password = "";
            }
        },
    },
    getters: {
        remaining: (state) => state.RATE_LIMIT - state.counter,
    },
});
