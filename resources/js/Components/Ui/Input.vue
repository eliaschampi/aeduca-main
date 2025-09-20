<script setup>
import { useField } from "vee-validate";
import { computed, toRef } from "vue";
const props = defineProps({
  name: String,
  modelValue: [String, Number, Date],
  label: {
    type: String,
    default: "",
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: " ",
  },
  step: String,
  type: {
    type: String,
    default: "text",
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

const inputClass = computed(() => {
  return {
    "form-control": true,
    "is-invalid": meta.touched && !meta.valid,
    "is-valid": meta.touched && meta.valid,
  };
});
</script>

<template>
  <div class="form-floating">
    <input
      :id="`${field}Id`"
      :name="field"
      :type="type"
      :value="inputValue"
      :disabled="disabled"
      :placeholder="placeholder"
      :step="step"
      :class="inputClass"
      @input="handleChange"
      @blur="handleBlur"
    />
    <label class="form-label" :for="`${field}Id`">
      {{ label }}
    </label>
    <span v-show="errorMessage || meta.valid" class="small text-danger">{{ errorMessage }}</span>
  </div>
</template>
