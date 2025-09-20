<script setup>
import { computed, ref, watch } from "vue";
import { destroyPyApi, fetchByRegisterPyApi } from "../../../http";
import { useCatStore } from "../../../pinia/cat";
import { useSnackbarStore } from "../../../pinia/snackbar";
import SetupNew from "./SetupNew.vue";
const payments = ref([]);
const { showSnack, successSnack } = useSnackbarStore();
const cats = computed(() => useCatStore().cats);
const columns = [
    "Tipo de pago",
    "Descripción",
    "Monto",
    "Fecha de pago",
    "Estado",
    "Realizar pago",
];
const addPay = ref(null);
const props = defineProps({
    registerCode: String,
    monthly: [String, Number],
});
const emit = defineEmits(["cancel"]);

function showPcodeLabel(code) {
    const value = cats.value.find((item) => item.code === code);
    return value?.name;
}

function handleDeletePayment(item) {
    showSnack({
        text: "¿Está seguro que desea eliminar el pago?",
        button: "Confirmar",
        action: async () => {
            const { data } = await destroyPyApi(item.code);
            successSnack(data.message);
            payments.value = payments.value.filter((item) => item.code !== item.code);
        },
    });
}

async function fetchData() {
    const { data } = await fetchByRegisterPyApi(props.registerCode);
    payments.value = data.values;
}

watch(
    () => props.registerCode,
    (value) => {
        if (value) {
            fetchData();
        } else {
            payments.value = [];
        }
    },
);
</script>
<template>
    <m-table
        :columns="columns"
        :data="payments"
        :head="false"
        emptytext="Aun no se han establecido los pagos"
    >
        <template #data="{ rows }">
            <tr v-for="item in rows" :key="item.code">
                <td>{{ showPcodeLabel(item.pcode) }}</td>
                <td>{{ item.description }}</td>
                <td>{{ $currency(item.amount) }}</td>
                <td>{{ $date(item.topay) }}</td>
                <td>
                    <span
                        v-if="item.paid"
                        class="badge bg-success-subtle text-success-emphasis"
                        :mytooltip="$date(item.paid)"
                    >
                        Pagado
                    </span>
                    <span v-else class="badge bg-warning-subtle text-warning-emphasis">
                        Pendiente
                    </span>
                </td>
                <td>
                    <div v-can="'AS'" v-year class="space">
                        <m-action @action="addPay.handleShowModal(item)" />
                        <m-action
                            icon="trash"
                            tool="Eliminar"
                            color="danger"
                            @action="handleDeletePayment(item)"
                        />
                        <m-action
                            v-show="!item.paid"
                            icon="cart"
                            color="primary-emphasis"
                            tool="Pagar"
                            @action="emit('cancel', item)"
                        />
                    </div>
                </td>
            </tr>
        </template>
    </m-table>
    <SetupNew
        ref="addPay"
        :cats="cats"
        :register-code="registerCode"
        :monthly="monthly"
        @after-add="fetchData"
    />
</template>
