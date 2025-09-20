<script setup>
const props = defineProps({
  route: String,
  title: String,
  icon: String,
  dni: String,
});
</script>

<template>
  <RouterLink v-slot="{ navigate, isActive }" custom :to="{ name: props.route, params: { dni: props.dni } }">
    <li
      class="nav-item pointer user-select-none"
      data-bs-toggle="tab"
      role="tab"
      :class="{ active: isActive }"
      @click="navigate"
    >
      <span class="nav-link d-flex align-items-center">
        <i class="icon" aria-hidden="true" :class="props.icon"></i>
        <span class="ms-2 d-none d-md-block d-lg-block">
          {{ props.title }}
        </span>
      </span>
    </li>
  </RouterLink>
</template>
