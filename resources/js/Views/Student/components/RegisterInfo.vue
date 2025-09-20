<script setup>
import { computed } from "vue";
import states from "../../../data/states.json";
import Empty from "../../../Components/Ui/Empty.vue";
import { useRegisterStore } from "../../../pinia/register";
const register = computed(() => useRegisterStore().register);
</script>

<template>
  <hr />
  <template v-if="register">
    <RouterLink v-slot="{ navigate }" custom :to="{ name: 'es_register', params: { dni: register.student_dni } }">
      <div class="border-rounded p-3 mx-auto space-b pointer" @click="navigate">
        <div class="text-title title">Matrícula {{ register.year }}</div>
        <div class="subtitle">
          <span class="fw-bold text-title"> {{ register.section_code.substr(-2) }} de </span>
          <b> {{ register.level }} </b>
          <small>({{ register.title }})</small>
        </div>
        <div class="badge" :class="states[register.state].badge">
          {{ states[register.state].text }}
        </div>
        <div>
          <span class="fw-bold"> Mes.</span>
          {{ $currency(register.monthly) }}
        </div>
      </div>
    </RouterLink>
  </template>
  <Empty v-else title="Aún no está matriculado" />
</template>
