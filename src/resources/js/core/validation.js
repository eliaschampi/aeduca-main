import { defineRule } from "vee-validate";
import { iso } from "./date";

const withEmpty =
    (ruleFn) =>
    (value, ...args) => {
        return !value || !value.length ? true : ruleFn(value, ...args);
    };

const required = (value) => {
    if (typeof value !== "number") {
        if (value === undefined || value === null || value === "") {
            return "Este campo es obligatorio";
        }
        if (typeof value === "object" && !Object.keys(value).length) {
            return "Este campo es obligatorio";
        }
    }
    return true;
};

const min = withEmpty((value, [min]) => {
    return value.length < min ? `Se requiere al menos ${min} caracteres` : true;
});

const max = withEmpty((value, [max]) => {
    return value.length > max ? `Máximo ${max} caracteres permitidos` : true;
});

const required_btw = (value, [min, max]) => {
    return !value || !value.length || !value.match(`^.{${min},${max}}$`)
        ? `Este campo debe ser un rango entre ${min} y ${max}`
        : true;
};

const len = (value, [length]) => {
    return value.length !== length ? `Este campo debe tener ${length} caracteres` : true;
};

const password = (value) => {
    const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!$_%])[A-Za-z\d!$_%]{6,}$/;
    return !regex.test(value) ? "Se requiere una mayuscula, un numero y mas de 6 caracteres" : true;
};

const confirmed = (value, [confirmed]) => {
    return value !== confirmed ? "Las contraseñas no coinciden" : true;
};

const email = withEmpty((value) => {
    const emailRex = /[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i;
    return !emailRex.test(value) ? "Este campo debe ser un correo válido" : true;
});

const numeric = withEmpty((value) => {
    const numericValue = Number(value);
    return isNaN(numericValue) ? "Este campo debe ser un número" : true;
});

const phone = withEmpty((value) => {
    const regex = /^\d{9}(,\d{9})*$/;
    return !regex.test(value) ? "Este campo debe ser un numero válido" : true;
});

const before = withEmpty((value, [date]) => {
    const dateValue = iso(new Date(value));
    return dateValue >= date ? "La fecha debe ser anterior" : true;
});

const after = withEmpty((value, [date]) => {
    const dateValue = iso(new Date(value));
    return dateValue <= date ? "La fecha debe ser posterior" : true;
});

const natural_max = (value, [max]) => {
    const nvalue = Number(value);
    return nvalue > max ? `Este campo debe ser menor a ${max}` : true;
};

const natural_min = (value, [min]) => {
    const nvalue = Number(value);
    return nvalue < min ? `Este campo debe ser mayor a ${min}` : true;
};

defineRule("required", required);
defineRule("min", min);
defineRule("max", max);
defineRule("required_btw", required_btw);
defineRule("len", len);
defineRule("email", email);
defineRule("password", password);
defineRule("confirmed", confirmed);
defineRule("numeric", numeric);
defineRule("phone", phone);
defineRule("before", before);
defineRule("after", after);
defineRule("natural_max", natural_max);
defineRule("natural_min", natural_min);
