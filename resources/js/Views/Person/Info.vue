<script setup>
import { computed } from "vue";
import { useEntityStore } from "../../composables/utility";
import { dformat } from "../../core/day";
const { getStore } = useEntityStore();
const props = defineProps({
  who: String,
});

const person = computed(() => {
  const store = getStore(props.who);
  return store[props.who];
});

const address = computed(() => {
  const { ubigeo, district, address } = person.value;
  return `${ubigeo}: ${district} ${address}`;
});

const route = computed(() => `new_${props.who}`);

const fulldate = (date) => dformat(date, "DD/MM/YYYY h:mm a");
</script>

<template>
  <section>
    <MPlain label="DNI:" :value="person.dni" />
    <MPlain label="Sexo:" :value="person.gender === 'M' ? 'Hombre' : 'Mujer'" />
    <MPlain label="Fecha de Nacimiento:" :value="$date(person.birthdate)" />
    <MPlain label="Correo:" :value="person.email" />
    <MPlain label="Celular:" :value="person.phone" />
    <MPlain label="Dirección:" :value="address" />
    <MPlain label="Fecha de Registro:" :value="fulldate(person.created_at)" />
    <MPlain label="Modificado el:" :value="fulldate(person.updated_at)" />
    <MPlain label="Observaciones:" :value="person.obs" />
    <slot></slot>
    <MRouter v-can="'AS'" :to="{ name: route, params: { dni: person.dni } }"> Modificar </MRouter>
  </section>
</template>
