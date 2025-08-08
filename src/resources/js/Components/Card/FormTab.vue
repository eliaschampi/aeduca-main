<script setup>
import { Form } from "vee-validate";
const props = defineProps({
    btnName: {
        type: String,
        default: "Guardar Registro",
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
    schema: Object,
});
const emit = defineEmits(["ok"]);

function handleSubmit() {
    if (props.isLoading) return;
    emit("ok");
}
function goBack() {
    history.back();
}
</script>
<template>
    <Form
        v-slot="{ meta: { valid, touched } }"
        class="card"
        :validation-schema="schema"
        autocomplete="off"
        @submit="handleSubmit"
    >
        <div class="card-body">
            <ul
                class="m-2 nav nav-line nav-fill"
                role="tablist"
            >
                <slot name="tabs" />
            </ul>
            <div class="tab-content">
                <slot />
            </div>
        </div>
        <div class="card-footer text-center">
            <m-submit
                :disabled="!valid || !touched"
                color="btn-success"
                :is-loading="isLoading"
            >
                {{ btnName }}
            </m-submit>
            <m-button
                color="btn-inverse-danger"
                @ok="goBack"
            >
                Cancelar
            </m-button>
            <slot name="foot" />
        </div>
    </Form>
</template>
