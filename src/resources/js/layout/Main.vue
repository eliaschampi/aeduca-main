<script setup>
import { computed, inject, onMounted, onUnmounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import Breadcrumb from "../Components/Ui/Breadcrumb.vue";
import Dialog from "../Components/Ui/Dialog.vue";
import SnackBar from "../Components/Ui/SnackBar.vue";
import Finder from "../Components/Views/Finder.vue";
import Refresh from "../Views/Auth/Refresh.vue";
import { useEntityStore, usePlaceholder } from "../composables/utility";
import { useBranchStore } from "../pinia/branch";
import { useModalStore } from "../pinia/modal";
import { useSnackbarStore } from "../pinia/snackbar";
import { useUserStore } from "../pinia/user";
import AppFooter from "./partial/Footer.vue";
import AppHeader from "./partial/Header.vue";
import AppSidebar from "./partial/Sidebar.vue";
const emitter = inject("emitter");
const { warningSnack } = useSnackbarStore();
const router = useRouter();
const route = useRoute();
const store = useBranchStore();
const { showModal, hideModal } = useModalStore();
const whoStored = computed(() => store.sfmOps.who);
const placeholder = computed(() => usePlaceholder(whoStored.value));
const studentDoesNotBelogHere = () => {
    if (whoStored.value === "student") {
        const { branch_code } = useUserStore();
        return person.value.branch_code !== branch_code;
    }
    return false;
};

const person = ref(null);

function handleHideOverlay() {
    document.querySelector(".sidebar-offcanvas").classList.remove("active");
    document.querySelector("#overlay").style.display = "none";
}

function handleNavbarEvent() {
    const navbar = document.querySelector(".navbar");
    if (window.matchMedia("(min-width: 991px)").matches) {
        if (window.scrollY >= 197) {
            navbar.classList.add("navbar-mini");
            navbar.classList.add("fixed-top");
            document.body.classList.add("navbar-fixed-top");
        } else {
            navbar.classList.remove("navbar-mini");
            navbar.classList.remove("fixed-top");
            document.body.classList.remove("navbar-fixed-top");
        }
    }
    if (window.matchMedia("(max-width: 991px)").matches) {
        navbar.classList.add("navbar-mini");
        navbar.classList.add("fixed-top");
        document.body.classList.add("navbar-fixed-top");
    }
}

function handleItemChange(value) {
    person.value = value;
}

function afterSelected() {
    if (store.sfmOps.mode === 1 && person.value.dni !== route.params.dni) {
        if (studentDoesNotBelogHere()) {
            warningSnack("El estudiante no pertenece a esta sede");
            return;
        }
        hideModal("finderModal");
        const { getStore } = useEntityStore();
        const { setPerson } = getStore(whoStored.value);
        setPerson(person.value);
        setTimeout(() => {
            router.push({
                name: `${whoStored.value}_profile`,
                params: {
                    dni: person.value.dni,
                },
            });
        }, 500);
    } else {
        emitter.emit("afterSelectPerson", person.value, whoStored.value);
    }
}

const handleFinderClosed = () => {
    if (whoStored.value === "student") {
        emitter.emit("sfm_has_closed");
    }
};

onMounted(() => {
    emitter.on("showFinderModal", () => {
        showModal("finderModal");
    });

    window.addEventListener("scroll", handleNavbarEvent);
});

onUnmounted(() => {
    window.removeEventListener("scroll", handleNavbarEvent);
});
</script>
<template>
    <div class="overflow-hidden d-flex">
        <AppSidebar />
        <div class="container-fluid page-body-wrapper">
            <AppHeader />
            <main class="main-panel">
                <div id="overlay" @click="handleHideOverlay"></div>
                <div class="content-wrapper">
                    <Breadcrumb class="d-block d-md-none" />
                    <RouterView v-slot="{ Component }">
                        <Transition name="fade" mode="out-in">
                            <component :is="Component" />
                        </Transition>
                    </RouterView>
                </div>
                <AppFooter />
            </main>
        </div>
        <Dialog
            id="finderModal"
            :title="placeholder"
            btn-name="Seleccionar"
            :disabled="!person"
            @cancel="handleFinderClosed"
            @ok="afterSelected"
        >
            <Finder @input="handleItemChange" />
        </Dialog>
        <Refresh />
        <SnackBar />
    </div>
</template>
