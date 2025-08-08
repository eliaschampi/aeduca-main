import axios from "axios";
import NProgress from "nprogress";
import "nprogress/nprogress.css";
import { useResponseStore } from "../pinia/response";
import { useUserStore } from "../pinia/user";
import "./nprogress.css";

const request = axios.create({
    baseURL: window.Laravel.api,
    timeout: 30000,
});

request.defaults.headers.common = {
    "X-CSRF-TOKEN": window.Laravel.csrf,
    "X-Requested-With": "XMLHttpRequest",
};

const errMessages = {
    404: "Recurso no encontrado",
    401: "Recurso no autorizado",
    403: "Recurso Denegado",
};

request.interceptors.request.use(
    (config) => {
        NProgress.start();
        const { token } = useUserStore();
        if (token) {
            config.headers["Authorization"] = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    },
);

request.interceptors.response.use(
    (response) => {
        NProgress.done();
        return response;
    },
    (error) => {
        NProgress.done();
        let errData = {
            code: navigator.onLine ? null : "offline",
            message: navigator.onLine ? null : "No tienes conexion a Internet",
        };
        if (error.response) {
            const { status, data } = error.response;
            let message = errMessages[status] ?? "Ocurrión un error inesperado";
            if (data.message && data.message.length < 60) {
                message = data.message;
            }
            if (status === 422 && data.errors) {
                const { errors: errs } = data;
                const firstKey = Object.keys(errs)[0];
                message = errs[firstKey][0] ?? "Error de validación";
            }
            Object.assign(errData, {
                code: status,
                message,
            });
        }
        const { handleResponseError } = useResponseStore();
        handleResponseError(errData);
        return Promise.reject(errData);
    },
);

export default request;
