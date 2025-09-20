<script setup>
import QrScanner from "qr-scanner";
import { inject, onMounted, onUnmounted, ref } from "vue";
import BlobsLoader from "../../Components/Ui/BlobsLoader.vue";
import Priority from "../../Components/Views/Priority.vue";
import { dformat } from "../../core/day.js";
import { autoAttApi } from "../../http";
import { useBranchStore } from "../../pinia/branch";
import { useModalStore } from "../../pinia/modal";
import { usePriorityStore } from "../../pinia/priority";
const emitter = inject("emitter");
const { hideModal } = useModalStore();

let qrScanner = null;

const video = ref(null);

const person = ref({
    profile: {
        image: "deleted.png",
    },
});

const cycle = ref("");

const is_dni = ref(false);

const state = ref("n");

const message = ref("");

const isStudent = ref(true);
const timer = ref(null);
const currentTime = ref(null);

const states = {
    n: "icon ion-md-cloudy text-primary",
    e: "icon ion-md-rainy text-danger",
    s: "icon ion-md-partly-sunny text-warning",
};

function ocultar() {
    timer.value = setTimeout(() => {
        state.value = "n";
        message.value = "";
        clearTimeout(timer.value);
    }, 3000);
}

async function handleSave(dni) {
    try {
        state.value = "l";
        const { priority } = usePriorityStore();
        const { data } = await autoAttApi({
            dni,
            type: isStudent.value ? "s" : "t",
            priority,
        });
        message.value = data.message;
        if (data.register !== null) {
            const { section_code, level } = data.register;
            cycle.value = `${section_code.substr(-2)} de ${level}`;
        } else {
            cycle.value = `Docente ${new Date().getFullYear()}`;
        }
        person.value = data.person;
        if (!data.person.profile) {
            person.value.profile = {
                image: "deleted.png",
            };
        }
        state.value = "s";
        new Audio(`media/${data.status}`).play();
    } catch (error) {
        message.value = error.message;
        state.value = "e";
        new Audio("media/error.mp3").play();
    } finally {
        ocultar();
    }
}

const startScanner = async () => {
    try {
        const hasCamera = await QrScanner.hasCamera();
        if (!hasCamera) {
            message.value = "No hay cámara disponible";
            return;
        }
        if (!qrScanner) {
            qrScanner = new QrScanner(
                video.value,
                ({ data }) => {
                    if (/^[0-9]{8}$/.test(data) && state.value === "n") {
                        handleSave(data);
                    }
                },
                {
                    maxScansPerSecond: 2,
                    highlightScanRegion: true,
                    highlightCodeOutline: true,
                },
            );
        }
        qrScanner.setGrayscaleWeights(85, 170, 1); //200, 50, 6;
        qrScanner.start();
    } catch {
        message.value = "Ha ocurrido un error al iniciar el escáner";
    }
};

function handleInputEnter(event) {
    const { value } = event.target;
    if (value && /^[0-9]{8,8}$/.test(value)) {
        handleSave(value);
        setTimeout(() => {
            document.getElementById("barcodeinput").value = "";
        }, 1000);
    }
}

function showFinder() {
    const who = isStudent.value ? "student" : "teacher";
    const { updateSfmOption } = useBranchStore();
    updateSfmOption({
        who,
        mode: 2,
        after: () => emitter.emit("showFinderModal"),
    });
}

function updateCurrentTime() {
    currentTime.value = dformat(new Date(), "h:mm:ss A");
}

const configureInterval = () => {
    currentTime.value = dformat(new Date(), "h:mm:ss A");
    setInterval(updateCurrentTime, 1000);
};

function afterSelect({ dni }) {
    hideModal("finderModal");
    handleSave(dni);
}

onMounted(() => {
    configureInterval();
    startScanner();
    emitter.on("afterSelectPerson", afterSelect);
});

onUnmounted(() => {
    if (qrScanner) {
        qrScanner.destroy();
    }
    emitter.off("afterSelectPerson", afterSelect);
});
</script>
<template>
    <section class="row">
        <div class="col-md-6">
            <div class="card left-panel">
                <div class="card-header">
                    <h5 class="d-flex space">
                        <span class="text-title">Horario:</span>
                        <Priority v-show="isStudent" @updated="ocultar" />
                        <i>{{ currentTime }}</i>
                    </h5>
                </div>
                <div class="card-body d-flex flex-column align-items-center space-b-1">
                    <m-check id="reader_type" v-model="is_dni" text="Ingresar DNI" />
                    <div v-show="!is_dni" class="qr-wrapper">
                        <video ref="video"></video>
                    </div>
                    <input
                        v-if="is_dni"
                        id="barcodeinput"
                        class="form-control"
                        style="max-width: 20rem; max-height: 22rem"
                        minlength="8"
                        maxlength="8"
                        placeholder="Ingrese DNI y presione ENTER"
                        @keypress.enter="handleInputEnter"
                    />
                </div>
                <div class="card-footer text-center">
                    <m-button
                        :color="isStudent ? 'btn-outline-success' : 'btn-warning'"
                        @ok="isStudent = !isStudent"
                    >
                        {{ isStudent ? "Estudiante" : "Docente" }}
                    </m-button>
                    <m-button color="btn-inverse-primary" @ok="showFinder"> Sin carnet </m-button>
                </div>
            </div>
        </div>
        <div class="col-md-6 mt-4 mt-md-0">
            <div class="card card-body d-flex flex-column align-items-center space-b-1">
                <BlobsLoader v-if="state === 'l'" />
                <template v-else-if="state === 's'">
                    <img
                        class="rounded-circle"
                        alt="Alumno"
                        :src="`/default/${person.profile.image}`"
                        style="width: 100px; height: 100px"
                    />
                    <h5>
                        {{ `${person.name} ${person.lastname}` }}
                    </h5>
                    <p class="text-primary">
                        <b>{{ cycle }}</b>
                    </p>
                </template>
                <span style="font-size: 5rem" :class="states[state]"></span>
                <alert
                    :type="state === 's' ? 'alert-success' : 'alert-warning'"
                    :dismisable="false"
                >
                    {{ message || "Esperando carnet" }}
                </alert>
            </div>
        </div>
    </section>
</template>
