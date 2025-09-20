<script setup>
import { computed, onMounted, ref, watch } from "vue";
import PersonLink from "../../Components/Views/PersonLink.vue";
import RelationModal from "../../Components/Views/RelationModal.vue";
import relation_types from "../../data/relationTypes.json";
import { fetchByStudentFmApi, removeStudentFmApi } from "../../http";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useStudentStore } from "../../pinia/student";
const storedDni = computed(() => useStudentStore().dni);
const { successSnack, showSnack } = useSnackbarStore();
const families = ref([]);
const columns = ["Apoderado", "Parentesco", "¿Encargado Principal?", "Celular", "Acciones"];

function handleDeleteFamily(item) {
  showSnack({
    text: "Eliminando apoderado",
    button: "Confirmar",
    action: async () => {
      const { data } = await removeStudentFmApi(item.dni, storedDni.value);
      families.value.splice(families.value.indexOf(item), 1);
      successSnack(data.message);
    },
  });
}

async function fetchData(dni = storedDni.value) {
  const { data } = await fetchByStudentFmApi(dni);
  families.value = data.values;
}

onMounted(() => {
  fetchData(storedDni.value);
});
watch(storedDni, (val) => fetchData(val));
</script>

<template>
  <MButton data-bs-toggle="modal" data-bs-target="#addRelation" color="btn-inverse-success" size="btn-sm">
    Agregar
  </MButton>
  <MTable emptytext="Estudiante aún no poseé apoderados." :columns="columns" :data="families" :head="false">
    <template #data="{ rows }">
      <tr v-for="item in rows" :key="item.dni">
        <td>
          <PersonLink route="family_profile" :person="item.person" />
        </td>
        <td>{{ relation_types[item.pivot.relation_type].name }}</td>
        <td>{{ item.pivot.is_main ? "Si" : "No" }}</td>
        <td>{{ item.person.phone }}</td>
        <td>
          <MAction icon="remove-circle" color="danger" tool="Excluir" @action="handleDeleteFamily(item)" />
        </td>
      </tr>
    </template>
  </MTable>
  <RelationModal title="apoderado" who="family" who-r="new_family" :dni="storedDni" @added="fetchData()" />
</template>
