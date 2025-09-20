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
        <m-plain label="DNI:" :value="person.dni" />
        <m-plain label="Sexo:" :value="person.gender === 'M' ? 'Hombre' : 'Mujer'" />
        <m-plain label="Fecha de Nacimiento:" :value="$date(person.birthdate)" />
        <m-plain label="Correo:" :value="person.email" />
        <m-plain label="Celular:" :value="person.phone" />
        <m-plain label="Dirección:" :value="address" />
        <m-plain label="Fecha de Registro:" :value="fulldate(person.created_at)" />
        <m-plain label="Modificado el:" :value="fulldate(person.updated_at)" />
        <m-plain label="Observaciones:" :value="person.obs" />
        <slot></slot>
        <m-router v-can="'AS'" :to="{ name: route, params: { dni: person.dni } }">
            Modificar
        </m-router>
    </section>
</template>
