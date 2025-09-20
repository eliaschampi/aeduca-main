import { dformat } from "./day";

const API = "http://localhost:8000";
export const ticket = (data, detail) => {
    const printData = {};

    printData.main = data;
    printData.detail = detail;
    printData.created_at = dformat(data.created_at, "DD [de] MMMM [del] YYYY h:mm A");

    return new Promise((resolve, reject) => {
        fetch(`${API}/print`, {
            method: "POST",
            body: JSON.stringify(printData),
        })
            .then((r) => {
                resolve(r.json());
            })
            .catch((error) => {
                reject(error);
            });
    });
};
