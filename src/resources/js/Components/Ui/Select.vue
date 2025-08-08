<script setup>
import { useField } from "vee-validate";
import { computed, toRef, watch } from "vue";

const modelValue = defineModel({ type: [String, Number, Object] });
const props = defineProps({
    name: String,
    label: String,
    options: {
        type: [Array, Object, Number],
        default: () => [],
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    optLabel: {
        type: [String, Function],
        default: "name",
    },
    optValue: {
        type: [String, Function],
        default: "code",
    },
    standalone: {
        type: Boolean,
        default: false,
    },
    placeholder: {
        type: String,
        default: "Selecciona una opción",
    },
});

const field = toRef(props, "name");

const { handleBlur, meta, errorMessage, setValue } = useField(field, undefined, {
    initialValue: modelValue,
    controlled: !props.standalone,
});

watch(modelValue, (value) => {
    setValue(value);
});

const isEmpty = computed(() => {
    const val = modelValue.value;
    if (typeof val === "number") {
        return false;
    }
    if (val === undefined || val === null || val === "") {
        return true;
    }
    if (typeof val === "object" && !Object.keys(val).length) {
        return true;
    }
    return false;
});

const selectClass = computed(() => {
    return {
        "form-select": true,
        "is-invalid": meta.touched && !meta.valid,
        "is-valid": meta.touched && meta.valid,
    };
});

function getLabel(option) {
    return typeof props.optLabel === "function" ? props.optLabel(option) : option[props.optLabel] || option;
}

function getOption(option, key) {
    return typeof props.optValue === "function" ? props.optValue(option, key) : option[props.optValue] || option;
}
</script>
<template>
    <div :class="{ 'form-floating': label }">
        <select
            :id="`${field}Id`"
            v-model="modelValue"
            :name="field"
            :disabled="disabled"
            :class="selectClass"
            @blur="handleBlur"
        >
            <slot>
                <option
                    v-if="isEmpty"
                    :value="modelValue"
                    disabled
                    hidden
                    selected
                >
                    {{ placeholder }}
                </option>
                <option
                    v-show="placeholder"
                    disabled
                >
                    {{ placeholder }}
                </option>
                <option
                    v-for="(option, key) in options"
                    :key="key"
                    :value="getOption(option, key)"
                >
                    {{ getLabel(option) }}
                </option>
            </slot>
        </select>
        <label
            v-show="label"
            class="form-label fw-medium"
            :for="`${field}Id`"
        >{{ label }}</label>
        <span
            v-show="errorMessage"
            class="invalid-feedback"
        >
            {{ errorMessage }}
        </span>
    </div>
</template>
