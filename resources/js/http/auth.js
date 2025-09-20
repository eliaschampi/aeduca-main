import axios from "axios";
import cache from "../core/cache";

export async function loginApi(payload) {
  try {
    const {
      data: { access_token, user },
    } = await axios({
      baseURL: window.Laravel.api,
      url: "/auth/login",
      headers: {
        "X-CSRF-TOKEN": window.Laravel.csrf,
        "X-Requested-With": "XMLHttpRequest",
      },
      method: "POST",
      data: payload,
    });
    cache.setItem("user", { ...user, access_token });
  } catch (error) {
    return Promise.reject(error.response.data);
  }
}
export async function recoverApi(payload) {
  try {
    const { data } = await axios({
      baseURL: window.Laravel.api,
      url: "/auth/recover",
      headers: {
        "X-CSRF-TOKEN": window.Laravel.csrf,
        "X-Requested-With": "XMLHttpRequest",
      },
      method: "PUT",
      data: payload,
    });
    return data;
  } catch (error) {
    return Promise.reject(error.response.data);
  }
}
