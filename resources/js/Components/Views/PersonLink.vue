<script setup>
const props = defineProps({
  route: String,
  person: Object,
});
</script>

<template>
  <RouterLink
    class="fw-bold"
    mytooltip="Ir a su perfil"
    :to="{
      name: props.route,
      params: { dni: props.person.dni },
    }"
  >
    {{ `${props.person.name} ${props.person.lastname}` }}
  </RouterLink>
</template>
