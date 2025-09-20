<script setup>
import { useForm } from "vee-validate";
import { computed, reactive, ref } from "vue";
import Dialog from "../../../Components/Ui/Dialog.vue";
import { fetchAllCrApi, surrenderCashApi } from "../../../http";
import { useModalStore } from "../../../pinia/modal";
import { useSnackbarStore } from "../../../pinia/snackbar";
const { successSnack } = useSnackbarStore();
const { hideModal } = useModalStore();

const types = [
    "Deposito Caja Cusco",
    "Deposito Bancario",
    "Entrega en efectivo",
    "Giro/Transferencia",
    "Otro",
];

const customers = ref([]);

const props = defineProps({
    cash: Object,
    fetchData: Function,
});

const surrender = reactive({
    amount: 0,
    recipient: "",
    reason: "Deposito Caja Cusco",
});

const schema = computed(() => {
    return {
        amount: {
            required: true,
            numeric: true,
            natural_min: 0,
            natural_max: props.cash ? props.cash.cash : 0,
        },
        recipient: {
            required: true,
        },
    };
});

const { handleSubmit } = useForm({
    validationSchema: schema,
});

const onSurrenderSubmit = handleSubmit(async (payload) => {
    const { data } = await surrenderCashApi(payload, props.cash.code);
    successSnack(data.message);
    hideModal("surrenderM");
    props.fetchData();
});
async function fetchCrs() {
    const { data } = await fetchAllCrApi();
    customers.value = data.values;
}
</script>
<template>
    <Dialog
        id="surrenderM"
        :title="'Rendir Caja: ' + cash.cash"
        @ok="onSurrenderSubmit"
        @open="fetchCrs"
    >
        <div class="space-b-1">
            <m-input
                v-model="surrender.amount"
                name="amount"
                type="number"
                label="Monto"
                step="0.01"
            />
            <m-select
                v-model="surrender.recipient"
                name="recipient"
                label="Responsable de recepción"
                opt-value="name"
                :options="customers"
            />
            <m-select
                v-model="surrender.reason"
                name="reason"
                label="Medio de entrega"
                :options="types"
            />
        </div>
    </Dialog>
</template>
