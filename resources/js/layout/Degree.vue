<script setup>
import { computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useCycleStore } from "../pinia/cycle";
import { useDegreeStore } from "../pinia/degree";
import { useModalStore } from "../pinia/modal";
import DegreeChoser from "./components/DegreeChoser.vue";
const { showModal } = useModalStore();
const router = useRouter();
const route = useRoute();
const degreeStore = useDegreeStore();
const { fetchCycles } = useCycleStore();

const cycleProps = computed(() => {
  const {
    degree: { cycle },
  } = degreeStore;
  if (!cycle) return {};
  return {
    level: cycle.full_name,
    title: cycle.title,
  };
});

function onChangedHandler() {
  router.push({
    name: route.name,
    params: { degree_code: degreeStore.code },
  });
}

const handleShowModal = async () => {
  await fetchCycles();
  degreeStore.fetchDegrees();
  showModal("degreeModal");
};

function fetchData() {
  const degreeCode = route.params.degree_code;
  if (degreeCode && (!degreeStore.exists || degreeStore.code !== degreeCode)) {
    try {
      degreeStore.fetchDegree(degreeCode);
    } catch {
      router.push({ name: route.name });
    }
  }
}

onMounted(fetchData);
</script>

<template>
  <div id="degreeContainer">
    <Panel class="mb-5 left-panel" style="max-width: 30rem">
      <div v-if="degreeStore.code" class="d-flex flex-wrap align-items-center space">
        <MButton
          style="min-width: 45px"
          color="btn-inverse-primary"
          class="btn-icon"
          icon="icon ion-md-switch"
          mytooltip="Click para cambiar"
          @ok="handleShowModal"
        />
        <h5 class="d-flex aling-items-center space">
          <span class="text-primary-emphasis">Nivel:</span>
          <span>{{ cycleProps.level }}</span>
          <i class="icon ion-md-information-circle text-body-secondary" :mytooltip="cycleProps.title"></i>
        </h5>
        <h5>
          <span class="text-primary-emphasis">Grado:</span>
          {{ degreeStore.degree.full_name }}
        </h5>
      </div>
      <h5 v-else class="d-flex align-items-center pointer" mytooltip="Click para seleccionar" @click="handleShowModal">
        Selecciona un grado
        <i class="ms-2 icon ion-md-hand text-success"></i>
      </h5>
    </Panel>
    <section v-if="degreeStore.exists">
      <template v-if="degreeStore.sections_count">
        <RouterView v-slot="{ Component }">
          <Transition name="fade" mode="out-in">
            <component :is="Component" />
          </Transition>
        </RouterView>
      </template>
      <Footext v-else title="Este grado aun no tiene secciones">
        <MRouter color="btn-inverse-success" :to="{ name: 'main_cycle' }"> Registrar nueva Seccion </MRouter>
      </Footext>
    </section>
    <DegreeChoser @changed="onChangedHandler" />
  </div>
</template>
