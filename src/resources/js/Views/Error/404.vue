<script setup>
const title = "¡Vaya! Tu solicitud no ha sido encontrado en el servidor";
const subtitle = "Es posible no que no exista o que haya sido borrado";
function goo() {
    history.back();
}
</script>
<template>
    <div class="error-box text-center">
        <h1 class="display-1 fw-bold text-secondary">
            <i class="icon ion-md-planet text-warning" />
            404
        </h1>
        <h2 class="fw-light text-white my-3">
            {{ title }}
            <br>
            <small>{{ subtitle }}</small>
        </h2>
        <m-button
            color="btn-success"
            @ok="goo"
        >
            Estar a salvo
        </m-button>
    </div>
</template>
