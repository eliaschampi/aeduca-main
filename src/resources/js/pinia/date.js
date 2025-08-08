import { defineStore } from "pinia";
import { isToday as isTodayUtility, iso, mym, tomorrow } from "../core/date";

export const useDateStore = defineStore({
    id: "date",
    state: () => ({
        date: iso(),
        range: {
            from: iso(),
            to: tomorrow(),
        },
        month: mym(),
    }),
    actions: {
        setRange(range) {
            this.range = range;
        },
        setDate(date) {
            this.date = date;
        },
    },
    getters: {
        isEmptyRange: (state) => {
            return !(state.range.from && state.range.to);
        },
        isToday: (state) => {
            const timeset = `${state.date}T00:00:00-05:00`;
            return isTodayUtility(timeset);
        },
    },
});
