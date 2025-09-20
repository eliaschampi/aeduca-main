import { createRouter, createWebHistory } from "vue-router";
import cache from "../core/cache.js";
import { useUserStore } from "../pinia/user.js";

import routes from "./routes.js";

const router = createRouter({
  history: createWebHistory(),
  linkActiveClass: "active",
  linkExactActiveClass: "active",
  routes,
});

router.beforeEach((to, from, next) => {
  const noauth = ["login", "not_found", "recover"];
  const auth = useUserStore();
  const isLogged = auth.user;

  if (!noauth.includes(to.name)) {
    if (isLogged) {
      const { rol_code, current_year: user_year } = isLogged;
      if (to.name === "login") {
        next({ name: "home" });
        return;
      }
      if (typeof to.meta.omitCheckYear === "undefined") {
        if (user_year !== new Date().getFullYear()) {
          next({ name: "not_auth" });
          return;
        }
      }
      if (typeof to.meta.roles !== "undefined") {
        if (!to.meta.roles.includes(rol_code)) {
          next({ name: "not_auth" });
          return;
        }
      }
      next();
    } else {
      cache.cleanAll();
      auth.user = null;
      next({ name: "login" });
    }
  } else {
    if (to.name === "login") {
      if (isLogged) {
        next({ name: "home" });
        return;
      }
    }
    next();
  }
});

export default router;
