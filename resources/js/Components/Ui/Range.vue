<script setup>
import { defineRule, Field, Form } from "vee-validate";
import { useDateStore } from "../../pinia/date";
import { useUserStore } from "../../pinia/user";

const emit = defineEmits(["fetch"]);
const { fullyear } = useUserStore();
const store = useDateStore();

defineRule("from", (value) => {
  if (!value) return "Campo Obligatorio";
  const initial = `${fullyear}-01-01`;
  if (value < initial || value >= store.range.to) {
    return "Elige una fecha anterior";
  }
  return true;
});

defineRule("to", (value) => {
  if (!value) return "Campo Obligatorio";
  const isNextYear = new Date(value).getFullYear() >= fullyear + 1;
  if (value <= store.range.from || isNextYear) {
    return "Elige una fecha posterior";
  }
  return true;
});

const schema = {
  from: {
    from: true,
  },
  to: {
    to: true,
  },
};

const fetchData = () => {
  emit("fetch");
};
</script>

<template>
  <Form
    v-slot="{ errors }"
    :validation-schema="schema"
    class="d-flex space justify-content-center justify-content-md-start align-items-center"
    @submit="fetchData"
  >
    <div class="d-flex space flex-wrap">
      <div class="resmar">
        <Field
          v-model="store.range.from"
          type="date"
          name="from"
          class="form-control"
          :class="{ 'is-invalid': errors.from }"
        />
      </div>
      <div class="resmar">
        <Field
          v-model="store.range.to"
          type="date"
          name="to"
          class="form-control"
          :class="{ 'is-invalid': errors.to }"
        />
      </div>
    </div>
    <button type="submit" class="btn btn-inverse-info mt-2 mt-md-0" icon="icon ion-md-search">buscar</button>
    <slot></slot>
  </Form>
</template>
