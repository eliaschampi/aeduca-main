<script setup>
import { useForm } from "vee-validate";
import { reactive } from "vue";
import Dialog from "../../../Components/Ui/Dialog.vue";

const detail = reactive({
    description: "",
    unit_price: "",
    quantity: "",
});

const { resetForm, handleSubmit } = useForm({
    validationSchema: {
        description: {
            required: true,
            max: 50,
        },
        unit_price: {
            required: true,
            natural_min: 0,
            natural_max: 10000,
        },
        quantity: {
            required: true,
            natural_min: 1,
            natural_max: 10000,
        },
    },
});

const emit = defineEmits(["ok"]);

function handleSave(values) {
    emit("ok", values);
}

function handleCancel() {
    resetForm({ errors: {} });
}

const onSubmit = handleSubmit(handleSave);
</script>
<template>
    <Dialog id="expenseDetailModal" btn-name="Agregar" @ok="onSubmit" @cancel="handleCancel">
        <m-input v-model="detail.description" name="description" label="Descripción" />
        <div class="row gx-2 my-4">
            <m-input
                v-model="detail.unit_price"
                class="col-md-6"
                name="unit_price"
                label="Precio"
                type="number"
                step="0.01"
            />
            <m-input
                v-model="detail.quantity"
                class="col-md-6"
                name="quantity"
                label="Cantidad"
                type="number"
            />
        </div>
        <h5 class="text-primary">Total: {{ $currency(detail.unit_price * detail.quantity) }}</h5>
    </Dialog>
</template>
