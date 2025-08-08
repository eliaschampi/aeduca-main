import { dcalendar, dformat } from "./day";

export const iso = (today = new Date()) => {
    const month = (today.getMonth() + 1).toString().padStart(2, "0");
    const day = today.getDate().toString().padStart(2, "0");
    return `${today.getFullYear()}-${month}-${day}`;
};

export const mym = () => {
    return (new Date().getMonth() + 1).toString().padStart(2, "0");
};

export const maxDate = () => iso(new Date());

export const date = (value = new Date()) => {
    return dformat(value, "DD [de] MMM [del] YYYY");
};

export const datetim = (value) => {
    return dformat(value, "DD [de] MMM. h:mm a");
};

export const ago = (value) => {
    return dcalendar(value);
};

export function isToday(value) {
    return new Date(value).toLocaleDateString() === new Date().toLocaleDateString();
}

export function tomorrow() {
    const value = new Date();
    value.setDate(value.getDate() + 1);
    return value.toISOString().split("T")[0];
}
