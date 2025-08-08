<script setup>
import { Form } from "vee-validate";
import { useRouter } from "vue-router";
const { go: goBack } = useRouter();
const props = defineProps({
    title: {
        type: String,
        default: "",
    },
    btnName: {
        type: String,
        default: "Guardar",
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
    schema: {
        type: Object,
        default: () => ({}),
    },
});
const emit = defineEmits(["ok"]);

function handleSubmit() {
    if (props.isLoading) return;
    emit("ok");
}
</script>

<template>
    <Form
        v-slot="{ meta }"
        class="card head-panel"
        autocomplete="off"
        :validation-schema="schema"
        @submit="handleSubmit"
    >
        <div class="card-header">
            <div class="card-title">{{ title }}</div>
        </div>
        <div class="card-body">
            <slot />
        </div>
        <div class="card-footer text-center">
            <m-submit
                color="btn-success"
                :disabled="!meta.valid"
                :is-loading="isLoading"
            >
                {{ btnName }}
            </m-submit>
            <m-button
                color="btn-outline-warning"
                @ok="goBack(-1)"
            >
                Cancelar
            </m-button>
        </div>
    </Form>
</template>
