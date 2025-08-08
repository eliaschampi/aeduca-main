<script setup>
const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
    modelValue: Boolean,
    dis: {
        type: Boolean,
        default: false,
    },
    id: {
        type: String,
        default: "mycheck",
    },
    text: {
        type: String,
        default: "",
    },
});

const emitChange = () => {
    emit("update:modelValue", !props.modelValue);
};
</script>
<template>
    <div class="d-flex">
        <div class="switch me-2">
            <input
                :id="props.id"
                type="checkbox"
                :checked="props.modelValue"
                :disabled="props.dis"
                @change="emitChange"
            >
            <label
                class="success"
                :for="props.id"
            >Toggle</label>
        </div>
        <label :for="props.id">
            {{ props.text }}
        </label>
    </div>
</template>
