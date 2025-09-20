import { useUserStore } from "../pinia/user";

export function checkPermission(rol_code, roles) {
    return roles.includes(rol_code);
}

export const CanDirective = {
    mounted(el, binding) {
        const {
            user: { rol_code },
        } = useUserStore();
        const isShow = checkPermission(rol_code, binding.value);
        if (!isShow) {
            el.style.display = "none";
        }
    },
};
