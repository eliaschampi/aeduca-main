import { defineStore } from "pinia";
import { onMounted, ref } from "vue";
import { cashersApi } from "../http";
import { useUserStore } from "./user";
import { useRoute } from "vue-router";
import { useDateStore } from "./date";
import { iso } from "../core/date";

export const useCasherStore = defineStore("casher", () => {
  const user_code = ref("");
  const cashers = ref([]);
  const route = useRoute();

  function setUser(payload) {
    user_code.value = payload;
  }

  async function refreshCasher() {
    const {
      user: { rol_code },
    } = useUserStore();
    if (rol_code === "A") {
      const { date: mdate, range: mrange } = useDateStore();
      const tomorrow = () => {
        const tom = new Date(mdate);
        tom.setDate(tom.getDate() + 2);
        return iso(tom);
      };
      const range = /(incomes|expense)/.test(route.name)
        ? mrange
        : {
            from: mdate,
            to: tomorrow(),
          };
      const { data } = await cashersApi(range.from, range.to);
      cashers.value = data.values;
    }
  }

  function userStored() {
    const {
      user: { code },
    } = useUserStore();
    return code;
  }

  onMounted(() => {
    setUser(userStored());
  });

  const isMe = () => {
    return user_code.value === userStored();
  };

  function hasUser() {
    return cashers.value.some((item) => item.code === user_code.value);
  }

  return {
    user_code,
    refreshCasher,
    setUser,
    cashers,
    hasUser,
    isMe,
  };
});
