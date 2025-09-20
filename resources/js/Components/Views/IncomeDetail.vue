<script setup>
import { Form } from "vee-validate";
import { computed, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import months from "../../data/months.json";
import { storeCachedDetailApi } from "../../http";
import { useCatStore } from "../../pinia/cat";
import { useDetailStore } from "../../pinia/detail";
import { useModalStore } from "../../pinia/modal";
import { useSnackbarStore } from "../../pinia/snackbar";
import Dialog from "../Ui/Dialog.vue";
const router = useRouter();
const route = useRoute();
const store = useDetailStore();
const emit = defineEmits(["ok", "cancel"]);
const { warningSnack } = useSnackbarStore();
const { hideModal } = useModalStore();
const cat = useCatStore();

const isMonthly = computed(() => store.detail.actiontype.name === "Mensualidad");

const schema = computed(() => ({
    actiontype: {
        required: store.isNew,
    },
    month: {
        required: isMonthly.value,
    },
    title: {
        max: 50,
    },
    topay: {
        required: true,
        natural_min: 0,
        natural_max: 10000,
    },
    paid: {
        required: true,
        natural_min: store.canceled ? store.detail.topay : 0,
        natural_max: store.detail.topay,
    },
    discount: {
        required: true,
        natural_min: 0,
        natural_max: store.detail.topay,
    },
}));

const pending = computed(() => {
    const { topay, discount, paid } = store.detail;
    return topay - discount - paid;
});

const newLabel = () => {
    const { month, title } = store.detail;
    const newTitle = isMonthly.value ? month : title;
    if (store.canceled) {
        return `${newTitle} (Cancelado)`;
    }
    if (pending.value > 0) {
        return `${newTitle} (Saldo Pendiente)`;
    }
    return newTitle;
};

function handleMyInputBlur() {
    const { topay, discount } = store.detail;
    let paid = topay;
    if (Number(discount) > 0) {
        paid = paid - Number(discount);
    }
    store.updateDetailByField({ field: "paid", value: paid });
}

const handleSubmit = async () => {
    if (pending.value < 0) {
        warningSnack("Su saldo pendiente no puede ser negativo");
        return;
    }
    const { data } = await storeCachedDetailApi({
        ...store.detail,
        title: newLabel(),
        pending: pending.value,
    });
    hideModal("invoiceModal");
    if (["invoice", "register"].includes(route.name)) {
        emit("ok", data);
    } else {
        setTimeout(() => {
            router.push({ name: "invoice" });
        }, 100);
    }
};

watch(() => store.detail.topay, handleMyInputBlur);
watch(() => store.detail.discount, handleMyInputBlur);

function handleCancel() {
    store.setDetail();
    store.setIsNew(true);
    emit("cancel");
}
</script>
<template>
    <Dialog
        id="invoiceModal"
        title="Agregar un Ingreso"
        @open="cat.fetchCats"
        @cancel="handleCancel"
    >
        <Form :validation-schema="schema" @submit="handleSubmit">
            <template v-if="store.isNew">
                <m-switch id="mycheck" v-model="store.canceled" text="Cancelando Saldo Anterior" />
                <hr />
            </template>
            <div class="row gx-2">
                <m-select
                    v-model="store.detail.actiontype"
                    name="actiontype"
                    label="Modalidad"
                    :disabled="!store.isNew"
                    class="col-md-6"
                    :options="cat.cats"
                    opt-value="all"
                />
                <template v-if="isMonthly">
                    <m-select
                        v-model="store.detail.month"
                        name="month"
                        label="Mes"
                        :disabled="!store.isNew"
                        class="col-md-6"
                        :options="months"
                        opt-value="name"
                    />
                </template>
                <m-input
                    v-else
                    v-model="store.detail.title"
                    :disabled="!store.isNew"
                    name="title"
                    class="col-md-6"
                    label="Concepto"
                    placeholder="opcional"
                />
            </div>
            <div class="row gx-2 mt-4">
                <m-input
                    id="Pagar"
                    v-model="store.detail.topay"
                    name="topay"
                    class="col-md-6 mb-4"
                    label="Monto a pagar:"
                    type="number"
                    step="0.01"
                />
                <m-input
                    v-model="store.detail.paid"
                    name="paid"
                    label="Importe"
                    class="col-md-6 mb-4"
                    type="number"
                    step="0.01"
                />
            </div>
            <div class="row gx-2">
                <m-input
                    v-model="store.detail.discount"
                    name="discount"
                    label="Descuento"
                    class="col-md-6 mb-4"
                    type="number"
                    step="0.01"
                />
                <div class="col-md-6">
                    <p class="text-primary">{{ newLabel() }}</p>
                    <p>Saldo: {{ $currency(pending) }}</p>
                </div>
            </div>
            <footext>
                <m-submit color="btn-warning">Agregar</m-submit>
            </footext>
        </Form>
        <template #foot>
            <footext title="Completa todos los campos para agregar un ingreso" />
        </template>
    </Dialog>
</template>
