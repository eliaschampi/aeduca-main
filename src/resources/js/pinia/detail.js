import { defineStore } from "pinia";

const initialState = {
    actiontype: {},
    month: "",
    title: "",
    topay: 0,
    paid: 0,
    discount: 0,
};

export const useDetailStore = defineStore({
    id: "detail",
    state: () => ({
        detail: initialState,
        canceled: false,
        isNew: true,
    }),
    actions: {
        setDetail(detail = initialState) {
            Object.assign(this.detail, detail);
        },
        setCanceled(canceled) {
            this.canceled = canceled;
        },
        setIsNew(isNew) {
            this.isNew = isNew;
        },
        updateDetailByField({ field, value }) {
            this.detail[field] = value;
        },
    },
});
