import { defineStore } from "pinia";
import cache from "../core/cache";
import { checkPermission } from "../directives/can";
import { changeStateUserApi, fetchAllUserApi, fetchRolesUserApi, fetchUserApi } from "../http";

export const useUserStore = defineStore("user", {
    state: () => ({
        user: cache.getItem("user"),
        users: [],
        roles: [],
    }),
    actions: {
        async fetchUser(code) {
            const { data } = await fetchUserApi(code);
            this.user = data.user;
        },
        async fetchUsers() {
            const { data } = await fetchAllUserApi();
            this.users = data.values;
        },
        async fetchRoles() {
            const { data } = await fetchRolesUserApi();
            this.roles = data.values;
        },
        async changeState(payload) {
            const { data } = await changeStateUserApi(payload);
            const item = this.users.find((item) => item.code === payload.code);
            if (item) {
                item.state = payload.state;
            }
            return data;
        },
        updateUserCachedProps(args) {
            const value = { ...this.user, ...args };
            cache.removeItem("counts");
            cache.setItem("user", value);
            this.user = value;
        },
        updateUserCached(user) {
            cache.removeItem("counts");
            cache.setItem("user", user);
            this.user = user;
        },
        isMe(user_code) {
            return user_code === this.user.code;
        },
    },
    getters: {
        actives: (state) => state.users.filter((item) => item.state),
        inactives: (state) => state.users.filter((item) => !item.state),
        fullname: (state) => `${state.user.name} ${state.user.lastname}`,
        image: (state) => `/default/${state.user.image}`,
        dni: (state) => state.user.dni,
        rol: ({ user }) => user.rol.name,
        branch: (state) => state.user.branch.name,
        branch_code: (state) => state.user.branch.code,
        fullyear: (state) => state.user.current_year,
        can(state) {
            return (roles) => checkPermission(state.user.rol.code, roles);
        },
        limited: (state) => state.user.access === "n",
        token: (state) => state.user.access_token,
        hasProfile: () => true,
    },
});
