<script setup>
import { computed, ref } from "vue";
import Empty from "../../Components/Ui/Empty.vue";
import { useEntityStore } from "../../composables/utility";
import { downl } from "../../core/util";
import { destroyProfileApi, printProfileInfoApi, storeProfileApi, updateProfileApi } from "../../http";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useUserStore } from "../../pinia/user";
const { fullyear } = useUserStore();
const { successSnack, showSnack } = useSnackbarStore();
const { getStore } = useEntityStore();
const props = defineProps({
  ptype: String,
});

const showpass = ref(false);

const isDifferentYear = computed(() => new Date().getFullYear() !== fullyear);

const person = computed(() => {
  const store = getStore(props.ptype);
  return store[props.ptype];
});

async function createProfile() {
  const img = person.value.gender === "M" ? "men" : "women";
  const { data } = await storeProfileApi({
    dni: person.value.dni,
    image: `default_${img}.png`,
  });
  successSnack(data.message);
  const { updateProfile } = getStore(props.ptype);
  updateProfile({
    person_dni: person.value.dni,
    last_logout: new Date(),
    original_password: data.original_password,
    image: `default_${img}.png`,
  });
}

function updatePassword() {
  showSnack({
    text: "¡Cuidado! Su contraseña actual será restablecido",
    button: "Confirmar",
    action: async () => {
      const { data } = await updateProfileApi(person.value.dni);
      successSnack(data.message);
      const { updateProfile } = getStore(props.ptype);
      updateProfile({
        ...person.value.profile,
        original_password: data.original_password,
      });
    },
  });
}
function deleteProfile() {
  showSnack({
    text: "Se eliminará el acceso a la plataforma",
    button: "Confirmar",
    action: async () => {
      const { data } = await destroyProfileApi(person.value.dni);
      successSnack(data.message);
      const { updateProfile } = getStore(props.ptype);
      updateProfile(null);
    },
  });
}
async function printInfo() {
  const { data } = await printProfileInfoApi(person.value.dni);
  downl(data, person.value.name, ".pdf");
}
</script>

<template>
  <Card title="Plataforma educativa" :f="false">
    <blockquote class="blockquote">
      Las <b>contraseñas prestablecidas</b> son generadas por el sistema. Tenga en cuenta que el estudiante o docente
      puede cambiarlo posteriormente.
    </blockquote>
    <template #rb>
      <div v-if="person.profile" v-can="'ANS'" class="d-flex space">
        <MAction v-show="!isDifferentYear" icon="icon ion-md-print" tool="Imprimir Ficha" @action="printInfo" />
        <MButton
          :disabled="isDifferentYear"
          size="btn-sm"
          color="btn-inverse-danger btn-icon"
          icon="icon ion-ios-remove-circle-outline"
          mytooltip="Eliminar información del perfil"
          @ok="deleteProfile"
        />
      </div>
      <MButton
        v-else
        :disabled="isDifferentYear"
        size="btn-sm"
        color="btn-inverse-success"
        icon="icon ion-md-create"
        @ok="createProfile"
      >
        Habilitar ahora
      </MButton>
    </template>
    <template v-if="person.profile">
      <MPlain label="Estado:">
        <span class="space">
          <i class="icon ion-md-key icon-sm text-success"></i>
          <span class="badge bg-success-subtle text-success-emphasis"> Habilitado </span>
        </span>
      </MPlain>
      <MPlain label="Últ. vez en línea:">
        <span class="space">
          <i class="icon ion-md-time icon-sm text-title"></i>
          <i>{{ $ago(person.profile.last_logout) }}</i>
        </span>
      </MPlain>
      <div class="row align-items-center mb-4">
        <label class="fw-bold col-form-label col-sm-3"> Contraseña: </label>
        <div class="col-sm-9 d-flex flex-wrap align-items-center">
          <b>{{ showpass ? person.profile.original_password : "*********" }}</b>
          <span class="ms-2 text-success pointer" @click="showpass = !showpass">
            <i class="icon icon-md" :class="[{ 'ion-md-eye': !showpass, 'ion-md-eye-off': !!showpass }]"></i>
          </span>
          <u class="ms-2 text-primary pointer" @click="updatePassword"> restablecer </u>
        </div>
      </div>
    </template>
    <template v-else>
      <Empty title="Su acceso no esta habilitado" />
    </template>
  </Card>
</template>
