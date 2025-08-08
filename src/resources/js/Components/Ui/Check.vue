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
    <div class="form-check">
        <input
            :id="props.id"
            :checked="props.modelValue"
            :disabled="props.dis"
            class="form-check-input"
            type="checkbox"
            @change="emitChange"
        />
        <label class="form-check-label user-select-none" :for="props.id">{{ props.text }}</label>
    </div>
</template>
