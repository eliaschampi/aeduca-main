<script setup>
import { computed, onMounted, ref, watch } from "vue";
import MainWrapper from "../../Components/Views/MainWrapper.vue";
import { fetchFmApi } from "../../http";
import { useFamilyStore } from "../../pinia/family";
import Info from "../Person/Info.vue";
import StudentList from "./StudentList.vue";

const family_dni = computed(() => useFamilyStore().dni);
const self = ref({});

async function fetchData(dni) {
  const { data } = await fetchFmApi(dni);
  self.value = data.value;
}

onMounted(() => {
  fetchData(family_dni.value);
});
watch(family_dni, (val) => fetchData(val));
</script>

<template>
  <MainWrapper ptype="family">
    <template #profile-info>
      <span class="badge bg-secondary">Apoderado</span>
    </template>
    <template #room>
      <StudentList />
    </template>
    <Panel title="Información del apoderado">
      <Info who="family">
        <MPlain label="Teléfono:" :value="self.telephone" />
        <MPlain label="Profesión:" :value="self.profession" />
        <MPlain label="Nivel de estudios:" :value="self.degree" />
        <MPlain label="Colegio:" :value="self.institute" />
      </Info>
    </Panel>
  </MainWrapper>
</template>
