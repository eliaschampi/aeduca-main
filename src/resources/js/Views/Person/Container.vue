<script setup>
import { computed, inject, onMounted, onUnmounted, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import BlobsLoader from "../../Components/Ui/BlobsLoader.vue";
import InputFinder from "../../Components/Views/InputFinder.vue";
import { useEntityStore } from "../../composables/utility";
import { fetchPersonApi } from "../../http";
import { useModalStore } from "../../pinia/modal";
const isLoading = ref(false);
const router = useRouter();
const route = useRoute();
const emitter = inject("emitter");
const { hideModal } = useModalStore();
const routeDni = computed(() => route.params.dni);
const ptype = computed(() => route.meta.ptype || "student");
const profileRoute = computed(() => `${ptype.value}_profile`);
const isMain = computed(() => route.name === profileRoute.value);
const { getStore } = useEntityStore();

const storedDni = computed(() => getStore(ptype.value).dni);

function setPerson(value) {
    getStore(ptype.value).setPerson(value);
}

const redirectToRoute = (name, dni) => router.push({ name, params: { dni } });

function afterSelect(person) {
    setPerson(person);
    if (routeDni.value !== storedDni.value) {
        redirectToRoute(route.value, storedDni.value);
    }
    hideModal("finderModal");
}

async function fetchData(dni = storedDni.value) {
    isLoading.value = true;
    try {
        const { data } = await fetchPersonApi(ptype.value, dni);
        setPerson(data.value);
    } catch {
        setPerson(null);
        router.push({ name: profileRoute.value });
    } finally {
        isLoading.value = false;
    }
}

function refreshData(dni) {
    if (!isMain.value) {
        redirectToRoute(profileRoute.value, dni);
    } else {
        fetchData(dni);
    }
}

watch(routeDni, (val) => {
    if (val) {
        fetchData(val);
    }
});

onMounted(() => {
    emitter.on("afterSelectPerson", afterSelect);

    if (!routeDni.value) {
        return;
    }

    const isValid = (dni) => /^[0-9]{8}$/.test(dni);

    if (!isValid(routeDni.value)) {
        router.push({ name: profileRoute.value });
        return;
    }

    if (routeDni.value !== storedDni.value) {
        fetchData(routeDni.value);
    }
});

onUnmounted(() => {
    emitter.off("afterSelectPerson", afterSelect);
});
</script>
<template>
    <div id="person_container">
        <panel
            class="mb-5"
            :title="route.meta.title"
        >
            <div class="d-flex flex-wrap align-items-center">
                <InputFinder
                    :who="ptype"
                    :only-current-reg="ptype !== 'student'"
                    :mode="2"
                />
                <m-button
                    class="mt-2 ms-md-2 mt-md-0"
                    color="btn-inverse-success"
                    :disabled="!storedDni || isLoading"
                    @ok="refreshData()"
                >
                    <template v-if="isLoading">
                        <i class="icon ion-ios-hourglass infinite-spin" />
                    </template>
                    <template v-else>
                        {{ isMain ? "Actualizar" : "Ir a Perfil" }}
                    </template>
                </m-button>
            </div>
        </panel>
        <section
            v-if="storedDni"
            id="info-container"
        >
            <RouterView v-slot="{ Component }">
                <Transition
                    name="fade"
                    mode="out-in"
                >
                    <component :is="Component" />
                </Transition>
            </RouterView>
        </section>
        <BlobsLoader v-else-if="isLoading" />
        <footext
            v-else
            :title="`No ha sido seleccionado ningún ${
                ptype === 'student' ? 'estudiante' : ptype === 'teacher' ? 'docente' : 'apoderado'
            }`"
        >
            <m-router
                v-can="'AS'"
                :to="{ name: `new_${ptype}` }"
            >
                Registrar ahora
            </m-router>
        </footext>
    </div>
</template>
