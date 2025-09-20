<script setup>
defineProps({
  modelValue: String,
  nofamily: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update:modelValue"]);

const updateValue = (value) => emit("update:modelValue", value);
</script>

<template>
  <div class="selectgroup selectgroup-pills">
    <label class="form-label selectgroup-item">
      <input
        class="selectgroup-input"
        name="persontyperadio"
        type="radio"
        value="student"
        :disabled="disabled"
        :checked="modelValue === 'student'"
        @change="updateValue('student')"
      />
      <span class="selectgroup-button">Estudiante</span>
    </label>
    <label class="form-label selectgroup-item">
      <input
        class="selectgroup-input"
        name="persontyperadio"
        type="radio"
        value="teacher"
        :disabled="disabled"
        :checked="modelValue === 'teacher'"
        @change="updateValue('teacher')"
      />
      <span class="selectgroup-button">Docente</span>
    </label>
    <label v-show="!nofamily" class="form-label selectgroup-item">
      <input
        class="selectgroup-input"
        name="persontyperadio"
        type="radio"
        value="family"
        :checked="modelValue === 'family'"
        @change="updateValue('family')"
      />
      <span class="selectgroup-button">Apoderado</span>
    </label>
  </div>
</template>
