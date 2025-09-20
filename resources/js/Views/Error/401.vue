<script setup>
const title = "¡Vaya! Este lugar esta restringido";
function goo() {
  history.back();
}
</script>

<template>
  <Panel style="max-width: 30rem">
    <h1 class="h1 text-secondary">401</h1>
    <h3 class="opacity-50">
      <i class="icon ion-md-bug text-warning"></i>
      {{ title }}
    </h3>
    <div class="text-secondary fw-bold">Considera:</div>
    <ol class="list-group list-group-flush list-group-numbered">
      <li class="list-group-item">Tu rol podría no tener permiso para realizar esta operación</li>
      <li class="list-group-item">
        Podrias estar en un año diferente a: {{ new Date().getFullYear() }}. Actualiza en la configuracion de tu perfil
      </li>
      <li class="list-group-item">Quiza ocurrió un incoveniente, solicita ayuda al soporte</li>
    </ol>

    <MButton class="mt-4" @ok="goo"> Estar a salvo </MButton>
  </Panel>
</template>
