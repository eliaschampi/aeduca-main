<script setup>
import { useField } from "vee-validate";
import { toRef } from "vue";

const props = defineProps({
    name: String,
    modelValue: String,
    label: {
        type: String,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        required: false,
    },
});

const field = toRef(props, "name");

const {
    value: inputValue,
    errorMessage,
    handleBlur,
    handleChange,
    meta,
} = useField(field, undefined, {
    initialValue: props.modelValue,
    syncVModel: true,
});
</script>
<template>
    <div class="form-floating">
        <textarea
            :id="`${field}Id`"
            :class="{ 'form-control': true, 'is-invalid': !!errorMessage, 'is-valid': meta.valid }"
            :name="field"
            :value="inputValue"
            :disabled="disabled"
            style="height: 6rem"
            @input="handleChange"
            @blur="handleBlur"
        ></textarea>
        <label class="form-label" :for="`${field}Id`">
            <slot>{{ label }}</slot>
        </label>
        <small v-show="errorMessage || meta.valid" class="small text-danger">{{
            errorMessage
        }}</small>
    </div>
</template>
