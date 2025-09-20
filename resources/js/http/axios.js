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
  }
);

request.interceptors.response.use(
  (response) => {
    NProgress.done();
    return response;
  },
  (error) => {
    NProgress.done();
    const errData = {
      code: navigator.onLine ? null : "offline",
      message: navigator.onLine ? null : "No tienes conexion a Internet",
    };
    if (error.response) {
      const { status, data } = error.response;
      const { errors, message } = data;
      let statusBasedMessage = errMessages[status] ?? "Ocurrión un error inesperado";

      if (message && message.length < 60) {
        statusBasedMessage = message;
      }
      if (status === 422 && errors) {
        const firstKey = Object.keys(errors)[0];
        statusBasedMessage = errors[firstKey][0] ?? "Error de validación";
      }
      Object.assign(errData, {
        code: status,
        message: statusBasedMessage,
      });
    }
    const { handleResponseError } = useResponseStore();
    handleResponseError(errData);
    return Promise.reject(errData);
  }
);

export default request;
