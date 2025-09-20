<script setup>
import { ref } from "vue";
import Dialog from "../../../Components/Ui/Dialog.vue";
import { date as formated } from "../../../core/date";
import { fetchLastCashApi } from "../../../http";
import { useUserStore } from "../../../pinia/user";
const { fullname } = useUserStore();
const emit = defineEmits(["ok"]);
const lastCash = ref("0.00");
const newCode = ref("");

async function fetchData() {
    const {
        data: { value },
    } = await fetchLastCashApi();
    lastCash.value = value.cash;
    newCode.value = value.code;
}
</script>
<template>
    <Dialog
        id="new_cash"
        title="Aperturar Caja"
        btn-name="Aperturar"
        @open="fetchData"
        @ok="emit('ok', newCode, lastCash)"
    >
        <m-plain label="Fecha" :value="formated()" />
        <m-plain label="Código:" :value="newCode" />
        <m-plain label="Usuario:" :value="fullname" />
        <div class="border-rounded p-3 mt-4" style="max-width: 100%">
            <div class="text-title title">Saldo anterior:</div>
            <input
                id="m-cash"
                class="form-control-plaintext form-control-lg"
                type="text"
                :value="`S/: ${lastCash}`"
                readonly
            />
        </div>
    </Dialog>
</template>
