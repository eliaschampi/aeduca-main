const storage = window.localStorage;

function base64EncodeUnicode(str) {
    const utf8Bytes = encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function (match, p1) {
        return String.fromCharCode(`0x${p1}`);
    });
    return btoa(utf8Bytes);
}

function base64DecodeUnicode(str) {
    const percentEncodedStr = atob(str)
        .split("")
        .map(function (c) {
            return `%${`00${c.charCodeAt(0).toString(16)}`.slice(-2)}`;
        })
        .join("");

    return decodeURIComponent(percentEncodedStr);
}

export default {
    setItem: (key, value) => {
        const encripted = base64EncodeUnicode(JSON.stringify(value));
        storage.setItem(key, encripted);
        return encripted;
    },

    getItem: (key) => {
        const value = storage.getItem(key);
        if (value) {
            return JSON.parse(base64DecodeUnicode(value));
        }
        return null;
    },

    removeItem: (key) => {
        return storage.removeItem(key);
    },

    cleanAll: () => {
        return storage.clear();
    },
};
