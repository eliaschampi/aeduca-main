<script setup>
import { computed, onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import FormTab from "../../Components/Card/FormTab.vue";
import { fetchAllBranchApi, fetchUserApi, setUserApi, updateUserApi } from "../../http";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useUserStore } from "../../pinia/user";
const { successSnack } = useSnackbarStore();
const userStored = useUserStore();
const route = useRoute();
const router = useRouter();
const branches = ref([]);
const schema = {
  name: {
    required_btw: [3, 20],
  },
  lastname: {
    required_btw: [10, 80],
  },
  dni: {
    required: true,
    numeric: true,
    len: 8,
  },
  email: {
    required: true,
    email: true,
  },
  phone: {
    required: true,
    phone: true,
    max: 9,
  },
  address: {
    required: true,
    max: 100,
  },
  rol_code: {
    required: true,
  },
  branch_code: {
    required: true,
  },
};

const user = reactive({
  gender: "M",
  name: "",
  lastname: "",
  dni: "",
  email: "",
  phone: "",
  address: "",
  rol_code: "",
  branch_code: "",
  code: "",
});

const isLoading = ref(false);
const isDiferentUser = computed(() => {
  return user.dni !== userStored.dni;
});

onMounted(async () => {
  await userStored.fetchRoles();
  await fetchBranches();
  if (option.value === "update_user") {
    fetchUser();
  }
});

const option = computed(() => route.name);
const btnName = computed(() => (option.value === "new_user" ? "Guardar" : "Modificar"));

async function fetchBranches() {
  const { data } = await fetchAllBranchApi();
  branches.value = data.values;
}

async function fetchUser() {
  try {
    const { data } = await fetchUserApi(route.params.code);
    Object.assign(user, data.value);
  } catch {
    router.push({ name: "not_found" });
  }
}

function storeData(values) {
  if (option.value === "new_user") {
    const { branch_code, rol_code, gender } = values;
    const ramdom = Math.floor(Math.random() * 90) + 10;
    const year = `${new Date().getFullYear()}`.slice(-2);
    values.code = `US${branch_code}${rol_code}${gender}A${year}${ramdom}`;
    return setUserApi(values);
  }
  return updateUserApi(values);
}

const onSubmit = async () => {
  isLoading.value = true;
  try {
    const { data } = await storeData(user);
    successSnack(data.message);
    router.push({ name: "main_user" });
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <FormTab :btn-name="btnName" :schema="schema" :is-loading="isLoading" @ok="onSubmit">
    <template #tabs>
      <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#main" role="tab">
          <i class="icon ion-md-person d-md-none"></i>
          <span class="d-none d-md-block d-lg-block">General</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#contact" role="tab">
          <i class="icon ion-md-call d-md-none"></i>
          <span class="d-none d-md-block d-lg-block">Contacto</span>
        </a>
      </li>
    </template>
    <div id="main" class="tab-pane fade in show active" role="tabpanel">
      <div class="card-body">
        <div class="row gx-2">
          <MInput v-model="user.name" name="name" label="Nombre" class="col-md-6 mb-4" />
          <MInput v-model="user.lastname" name="lastname" label="Apellidos" class="col-md-6 mb-4" />
        </div>
        <div class="row gx-2">
          <MInput v-model="user.dni" name="dni" label="DNI" class="col-md-6 mb-4" />
          <MSelect v-model="user.gender" name="gender" label="Sexo" class="col-md-6 mb-4">
            <option value="M">Masculino</option>
            <option value="F">Femenino</option>
          </MSelect>
        </div>
        <div v-show="isDiferentUser" class="row gx-2">
          <MSelect
            v-model="user.rol_code"
            name="rol_code"
            label="Rol"
            class="col-md-6 mb-4"
            :options="userStored.roles"
          />
          <MSelect
            v-model="user.branch_code"
            name="branch_code"
            label="Sede"
            class="col-md-6 mb-4"
            :options="branches"
          />
        </div>
      </div>
    </div>
    <div id="contact" class="tab-pane fade" role="tabpanel">
      <div class="card-body">
        <div class="row gx-2">
          <MInput v-model="user.email" name="email" label="Correo" class="col-md-6 mb-4" />
          <MInput v-model="user.phone" name="phone" label="Celular" class="col-md-6 mb-4" />
        </div>
        <div class="row gx-2">
          <MInput v-model="user.address" name="address" label="Direccion" class="col-md-6 mb-4" />
        </div>
      </div>
    </div>
  </FormTab>
</template>
