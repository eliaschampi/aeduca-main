<script setup>
import MainWrapper from "../Components/Views/MainWrapper.vue";
import { useUserStore } from "../pinia/user";

const { user, updateUserCachedProps } = useUserStore();

function imgUploaded(image) {
  updateUserCachedProps({
    image,
  });
}
</script>

<template>
  <MainWrapper :pid="user.code" has-profile @img-uploaded="imgUploaded">
    <template #profile-info>
      <div class="d-flex flex-column align-items-center space-b">
        <div class="badge bg-success-subtle text-success-emphasis border border-success border-1">
          {{ user.rol.name }}
        </div>
      </div>
    </template>
    <template #profile-foot>
      <div class="my-2 mx-auto">
        <div><b>Sede:</b> {{ user.branch.name }}</div>
      </div>
    </template>
    <template #room>
      <Panel>
        <div class="list-group list-group-flush">
          <RouterLink class="list-group-item" :to="{ name: 'user_profile' }"> Mi perfil </RouterLink>
          <RouterLink class="list-group-item" :to="{ name: 'user_password' }"> Seguridad </RouterLink>
          <RouterLink class="list-group-item" :to="{ name: 'user_branch' }"> Cambiar de sede </RouterLink>
          <RouterLink class="list-group-item" :to="{ name: 'user_year' }"> Año académico </RouterLink>
          <RouterLink class="list-group-item" :to="{ name: 'user_sign' }"> Inicios de sesión </RouterLink>
        </div>
      </Panel>
    </template>
    <RouterView v-slot="{ Component }">
      <Transition name="fade" mode="out-in">
        <component :is="Component" />
      </Transition>
    </RouterView>
  </MainWrapper>
</template>
