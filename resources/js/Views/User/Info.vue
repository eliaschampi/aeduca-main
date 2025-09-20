<script setup>
import { useUserStore } from "../../pinia/user";

const { user } = useUserStore();
</script>

<template>
  <Panel title="Datos personales" f>
    <MPlain label="Código:" :value="user.code" />
    <MPlain label="DNI:" :value="user.dni" />
    <MPlain label="Email:" :value="user.email" />
    <MPlain label="Celular:" :value="user.phone" />
    <MPlain label="Dirección:" :value="user.address" />
    <MPlain label="Año:" :value="user.current_year" />
    <MPlain label="Nivel de acceso:" :value="user.access" />
    <template #foot>
      <MRouter
        color="btn-success"
        :to="{
          name: 'update_user',
          params: { code: user.code },
        }"
      >
        Modificar
      </MRouter>
    </template>
  </Panel>
</template>
