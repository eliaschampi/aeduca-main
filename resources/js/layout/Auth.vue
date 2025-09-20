<script setup>
import { useAppStore } from "../pinia/app";

const app = useAppStore();
</script>

<template>
  <section class="myvh100 bg-login overflow-hidden">
    <div id="stars"></div>
    <div id="stars2"></div>
    <div id="stars3"></div>
    <div class="container px-4 py-5 px-md-5 text-center text-lg-start mt-0 mt-md-5">
      <div class="row gx-lg-5 align-items-center mb-5">
        <div class="col-lg-6" style="z-index: 10">
          <h1 class="mt-0 mt-md-5 mb-2 display-5 fw-bold ls-tight text-warning">
            {{ app.myapp }} {{ app.version }}
            <br />
            <small class="text-primary"> Sistema administrativo para la educación del futuro </small>
          </h1>
          <p class="mb-2 text-white opacity-75 d-none d-md-block">
            {{ app.myapp }} es una plataforma educativa integral en la nube que ofrece funcionalidades de gestión de
            procesos académicos y administrativos de forma centralizada y eficiente, tambien provee funciones
            interactivas y contenido educativo bajo demanda para su comunidad de usuarios.
          </p>
        </div>
        <div class="col-lg-6 mb-5 mb-lg-0">
          <RouterView />
        </div>
      </div>
    </div>
  </section>
</template>
