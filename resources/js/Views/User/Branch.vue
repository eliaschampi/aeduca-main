<script setup>
import { Form } from "vee-validate";
import { onMounted, ref } from "vue";
import { changeBranchUserApi, fetchAllBranchApi } from "../../http";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useUserStore } from "../../pinia/user";
const { successSnack, warningSnack } = useSnackbarStore();
const branches = ref([]);
const branch = ref(null);

onMounted(fetchBranches);

async function fetchBranches() {
  const { data } = await fetchAllBranchApi();
  const { branch_code } = useUserStore();
  branches.value = Object.values(data.values).filter((item) => item.code !== branch_code);
}

async function handleChangeBranch() {
  const { code, name, is_academy: ia } = branch.value;
  const {
    user: { access: a },
    updateUserCachedProps,
  } = useUserStore();
  if (/(a|c|n)/.test(a)) {
    if ((a === "a" && !ia) || (a === "c" && ia) || a === "n") {
      warningSnack("Se ha restringido tu acceso a esta sede");
      return;
    }
  }
  const { data } = await changeBranchUserApi(code);
  successSnack(data.message);
  updateUserCachedProps({
    branch_code: code,
    branch: {
      code,
      name,
    },
  });
  setTimeout(() => {
    location.reload();
  }, 500);
}
</script>

<template>
  <Form :validation-schema="{ branch: 'required' }" @submit="handleChangeBranch">
    <Card title="Cambiar sede">
      <Alert type="alert-warning">
        Esta opción te permite acceder a otra sede. Recuerda consultar previamente con el encargado de la sede.
      </Alert>
      <MSelect v-model="branch" name="branch" label="Sede" :options="branches" opt-value="all" />
      <template #foot>
        <MSubmit>Cambiar</MSubmit>
      </template>
    </Card>
  </Form>
</template>
