<script setup>
import { computed, inject, onMounted, onUnmounted, ref } from "vue";
import { RouterLink, useRoute, useRouter } from "vue-router";
import { useLogout } from "../../composables/utility";
import { dformat } from "../../core/day.js";
import { useBranchStore } from "../../pinia/branch";
import { useThemeStore } from "../../pinia/theme";
import { useUserStore } from "../../pinia/user";

const { showCash, logOutAndReload } = useLogout();
const emitter = inject("emitter");
const route = useRoute();
const routeTitle = computed(() => route.meta.title);
const currentTime = ref(null);
const timer = ref(null);
const userStored = useUserStore();
const { go: goBack } = useRouter();
const themeStore = useThemeStore();
onMounted(() => {
    currentTime.value = dformat(new Date(), "dddd DD [de] MMMM h:mm A");
    timer.value = setInterval(() => updateCurrentTime(), 60000);
});

const title = computed(() => `${userStored.branch} ${userStored.fullyear}`);

const toggleTheme = computed(() => {
    if (themeStore.theme === "dark") {
        return {
            theme: "light",
            icon: "ion-md-sunny",
            text: "Enciende las luces",
        };
    }
    return {
        theme: "dark",
        icon: "ion-md-moon",
        text: "Apaga las luces",
    };
});

function updateCurrentTime() {
    currentTime.value = dformat(new Date(), "dddd DD [de] MMMM h:mm A");
}

function showFinder(who) {
    const { updateSfmOption } = useBranchStore();
    updateSfmOption({
        who,
        mode: 1,
        onlyCurrentReg: false,
        onlyCurrentBranch: false,
        after: () => {
            emitter.emit("showFinderModal");
        },
    });
}

onUnmounted(() => {
    clearInterval(timer.value);
});
</script>
<template>
    <nav
        class="navbar container-fluid px-0 py-0 py-lg-2 d-flex flex-row"
        :style="`background-image: url('/default/user-info${userStored.branch_code}.jpg');`"
    >
        <div class="navbar-menu-wrapper blurred d-flex align-items-center p-3">
            <div class="navbar-brand-wrapper">
                <RouterLink class="navbar-brand brand-logo" to="/">
                    <img src="/img/carrion.svg" alt="logo" />
                </RouterLink>
            </div>
            <h5 class="text-white mb-0 mt-1">
                {{ title }}
            </h5>
            <ul class="navbar-nav d-flex flex-row align-items-center navbar-nav-end space-1">
                <li class="nav-item dropdown">
                    <a
                        id="messageDropdown"
                        class="nav-link dropdown-toggle"
                        data-bs-toggle="dropdown"
                        href="javascript:void(0)"
                    >
                        <i class="icon ion-md-search"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="searchDropdown">
                        <button class="dropdown-item" @click="showFinder('student')">
                            <i class="icon ion-md-person text-secondary"></i>
                            Buscar estudiante
                        </button>
                        <button class="dropdown-item" @click="showFinder('family')">
                            <i class="icon ion-md-person text-success"></i>
                            Buscar apoderado
                        </button>
                        <button class="dropdown-item" @click="showFinder('teacher')">
                            <i class="icon ion-md-person text-primary"></i>
                            Buscar docente
                        </button>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <RouterLink
                        v-if="showCash"
                        id="cashitem"
                        class="nav-link d-flex align-items-center justify-content-center"
                        :to="{ name: 'cash' }"
                    >
                        <i class="icon ion-md-cart mx-0"></i>
                    </RouterLink>
                </li>
                <li class="nav-item dropdown">
                    <a
                        id="profileDropdown"
                        class="nav-link dropdown-toggle"
                        data-bs-toggle="dropdown"
                        href="javascript:void(0)"
                    >
                        <img alt="profile" class="rounded-circle" :src="userStored.image" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <div class="dropdown-header text-center fw-medium text-title">
                            <span>{{ userStored.fullname }}</span>
                        </div>
                        <RouterLink class="dropdown-item" :to="{ name: 'user_profile' }">
                            <i class="icon ion-md-person text-secondary"></i>
                            Mi Perfil
                        </RouterLink>
                        <RouterLink class="dropdown-item" :to="{ name: 'about' }">
                            <i class="icon ion-md-information-circle text-success"></i> Acerca de
                        </RouterLink>
                        <div class="dropdown-divider my-1"></div>
                        <a class="dropdown-item" @click="logOutAndReload">
                            <i class="icon ion-ios-log-out text-primary"></i>
                            Cerrar Sesión
                        </a>
                        <div class="dropdown-item" @click="themeStore.change(toggleTheme.theme)">
                            <i class="icon text-warning" :class="toggleTheme.icon"></i>
                            {{ toggleTheme.text }}
                        </div>
                    </div>
                </li>
                <li class="toggler pointer">
                    <i class="icon icon-md ion-md-menu text-white"></i>
                </li>
            </ul>
        </div>
        <div class="navbar-menu-wrapper toolbar-pane d-none d-lg-flex align-items-center px-4 mx-4">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <div class="d-flex align-items-center" style="width: 100%">
                        <div class="d-flex me-2 vl">
                            <RouterLink to="/">
                                <i class="icon ion-md-home icon-md"></i>
                            </RouterLink>
                            <div class="text-success pointer mx-2" @click="goBack(-1)">
                                <i class="icon ion-md-arrow-round-back icon-md"></i>
                            </div>
                        </div>
                        <h5 class="mb-0">
                            {{ routeTitle }}
                        </h5>
                    </div>
                </li>
            </ul>
            <h5 class="mb-0 ms-auto">
                {{ currentTime }}
            </h5>
        </div>
    </nav>
</template>
