<script setup>
import { onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import FormTab from "../../Components/Card/FormTab.vue";
import DistrictModal from "../../Components/Views/DistrictModal.vue";
import { maxDate } from "../../core/date";
import { fetchCyclesApi, fetchSimilarsApi } from "../../http";
import axios from "../../http/axios.js";
import { useDNIStore } from "../../pinia/dni";
import { useModalStore } from "../../pinia/modal";
import { useSnackbarStore } from "../../pinia/snackbar";
const { successSnack, showSnack } = useSnackbarStore();
const { hideModal } = useModalStore();
const isLoading = ref(false);
const similars = ref([]);
const router = useRouter();
const {
    params: { dni: routeDni },
    name: routeName,
} = useRoute();

const isS = routeName === "new_student";
const isT = routeName === "new_teacher";
const isF = routeName === "new_family";

const schema = {
    dni: {
        required: true,
        len: 8,
        numeric: true,
    },
    name: {
        required_btw: [3, 50],
    },
    lastname: {
        required_btw: [10, 80],
    },
    birthdate: {
        required: isS,
        before: maxDate(),
    },
    gender: {
        required: true,
    },
    ubigeo: {
        required: true,
        min: 8,
    },
    address: {
        required_btw: [10, 100],
    },
    email: {
        required: isT,
        email: true,
        max: 50,
    },
    phone: {
        required: true,
        phone: true,
        max: 29,
    },
    obs: {
        min: 10,
        max: 200,
    },
    // teacher
    specialty: {
        required: isT,
    },
    startdate: {
        required: isT,
        before: maxDate(),
    },
    // family
    profession: {
        required: isF,
        min: 6,
        max: 50,
    },
    degree: {
        required: isF,
    },
    telephone: {
        numeric: isF,
        min: 6,
        max: 20,
    },
    institute: {
        min: 5,
        max: 150,
    },
};

const person = reactive({
    dni: "",
    name: "",
    lastname: "",
    birthdate: "",
    gender: "M",
    ubigeo: "",
    district: "",
    address: "",
    email: "",
    phone: "",
    obs: "",
    //teacher
    specialty: "",
    startdate: "",
    //family
    profession: "",
    degree: "",
    telephone: "",
    institute: "",
});

const specialties = ref([]);

const levels = ["No Espesificado", "Basica", "Superior", "Universitaria", "Postgrado"];

const entity = routeName.substring(4);

async function fetchCycles() {
    const { data } = await fetchCyclesApi();
    specialties.value = data.values;
}

async function fetchData() {
    if (isT) {
        await fetchCycles();
    }
    if (routeDni) {
        const { data } = await axios.get(`${entity}/${routeDni}`);
        const {
            person: { ...relation },
            ...self
        } = data.value;
        Object.assign(person, { ...relation, ...self });
    }
}

onMounted(fetchData);

function handleAddDistrict(code, name) {
    person.ubigeo = code;
    person.district = name;
    hideModal("district");
}

async function fetchSimilars(name, lastname) {
    const fullname = encodeURIComponent(`${name} ${lastname}`);
    const { data } = await fetchSimilarsApi(fullname);
    similars.value = data.values;
}

function store(payload) {
    if (routeDni) {
        return axios.put(`/${entity}/${routeDni}`, payload);
    }
    return axios.post(`/${entity}`, payload);
}

const handleGoAdvance = (dni) => {
    showSnack({
        text: "Si sales de esta página perderas la información",
        button: "Confirmar",
        action: () => {
            const dniStore = useDNIStore();
            dniStore.$patch({
                dni,
            });
            router.push({ name: "entitymanage" });
        },
    });
};

const theresSimilars = () => similars.value.filter((s) => !s.checked).length > 0;

const onSubmit = async () => {
    try {
        isLoading.value = true;
        if (!routeDni && similars.value.length === 0) {
            await fetchSimilars(person.name, person.lastname);
        }
        let payload = person;

        if (isT) {
            const { startdate, specialty, dni, ...other } = person;
            Object.assign(payload, { ...other, sub: { dni, specialty, startdate } });
        } else if (isF) {
            const { profession, degree, telephone, institute, dni, ...other } = person;
            Object.assign(payload, {
                ...other,
                sub: { dni, profession, degree, telephone, institute },
            });
        }
        if (!theresSimilars()) {
            const { data } = await store(payload);
            successSnack(data.message);
            router.push({ name: `${entity}_profile`, params: { dni: person.dni } });
        }
    } finally {
        await new Promise((resolve) => setTimeout(resolve, 1000));
        isLoading.value = false;
    }
};
</script>
<template>
    <section>
        <alert>
            <span class="fw-medium">Importante:</span> Completa todos los campos requeridos con
            información previamente verificada.
        </alert>
        <FormTab
            :btn-name="routeDni ? 'Modificar' : 'Guardar'"
            :schema="schema"
            :is-loading="isLoading"
            @ok="onSubmit"
        >
            <template #tabs>
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#main" role="tab">
                        <i class="icon ion-md-person d-md-none"></i>
                        <span class="d-none d-md-block d-lg-block"> Datos Personales </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#additional" role="tab">
                        <i class="icon ion-md-information-circle-outline d-md-none"></i>
                        <span class="d-none d-md-block d-lg-block"> Información Adicional </span>
                    </a>
                </li>
            </template>
            <div id="main" class="tab-pane fade show active" role="tabpanel">
                <div class="row gx-2 mt-4">
                    <m-input
                        v-model="person.name"
                        label="Nombre"
                        name="name"
                        class="col-md-6 mb-4"
                    />
                    <m-input
                        v-model="person.lastname"
                        label="Apellidos"
                        name="lastname"
                        class="col-md-6 mb-4"
                    />
                </div>
                <div class="row gx-2">
                    <m-input
                        v-model="person.dni"
                        name="dni"
                        label="DNI"
                        class="col-md-6 mb-4"
                        :disabled="!!routeDni"
                    />
                    <m-select
                        v-model="person.gender"
                        label="Sexo"
                        name="gender"
                        class="col-md-6 mb-4"
                    >
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </m-select>
                </div>
                <div class="row gx-2">
                    <m-input
                        v-model="person.email"
                        label="Correo"
                        name="email"
                        class="col-md-6 mb-4"
                    />
                    <m-input
                        v-model="person.phone"
                        name="phone"
                        class="col-md-6 mb-4"
                        label="Celular"
                        placeholder="Si hay varios separa con (,)"
                    />
                </div>
            </div>
            <div id="additional" class="tab-pane fade" role="tabpanel">
                <div class="row gx-2 mt-4 align-items-center">
                    <div class="col-md-6 mb-4 d-flex align-items-center space-1">
                        <m-input
                            :model-value="`${person.ubigeo}: ${person.district}`"
                            class="w-100"
                            label="Distrito"
                            name="ubigeo"
                            disabled
                        />
                        <m-action
                            icon="add"
                            color="info"
                            tool="Agregar Distrito"
                            data-bs-toggle="modal"
                            data-bs-target="#district"
                        />
                    </div>
                    <m-input
                        v-model="person.address"
                        name="address"
                        label="Dirección"
                        class="col-md-6 mb-4"
                    />
                </div>
                <template v-if="isF">
                    <div class="row gx-2">
                        <m-input
                            v-model="person.profession"
                            name="profession"
                            class="col-md-6 mb-4"
                            label="Profesión u Ocupación"
                        />
                        <m-select
                            v-model="person.degree"
                            name="degree"
                            label="Nivel de estudio"
                            :options="levels"
                            class="col-md-6 mb-4"
                        />
                    </div>
                    <div class="row gx-2">
                        <m-input
                            v-model="person.telephone"
                            name="telephone"
                            label="Télefono de casa"
                            class="col-md-6 mb-4"
                        />
                        <m-input
                            v-model="person.institute"
                            name="institute"
                            class="col-md-6 mb-4"
                            label="Colegio de educación básica regular"
                        />
                    </div>
                </template>
                <div v-else-if="isT" class="row gx-2">
                    <m-select
                        v-model="person.specialty"
                        name="specialty"
                        label="Especialidad"
                        class="col-md-6 mb-4"
                        :options="specialties"
                        :opt-value="(_, key) => key"
                    />
                    <m-input
                        v-model="person.startdate"
                        name="startdate"
                        class="col-md-6 mb-4"
                        type="date"
                        label="Fecha de Ingreso"
                    />
                </div>
                <div class="row gx-2">
                    <m-input
                        v-model="person.birthdate"
                        name="birthdate"
                        type="date"
                        class="col-md-6 mb-4"
                        label="Fecha de nacimiento"
                    />
                    <m-input
                        v-model="person.obs"
                        name="obs"
                        label="Observaciones"
                        class="col-md-6 mb-4"
                    />
                </div>
            </div>
            <ul class="list-group myround bg-body p-3">
                <li v-if="isLoading || similars.length" class="list-group-item">
                    <strong class="text-success">
                        {{
                            isLoading
                                ? "Espere, estamos verificando "
                                : `Se han encontrado ${similars.length} `
                        }}
                        posibles duplicados
                    </strong>
                    <i class="d-block">Verifica antes para evitar duplicados</i>
                </li>
                <template v-for="dp in similars" :key="dp.dni">
                    <li class="list-group-item space-1">
                        <div class="d-flex align-items-center space">
                            <span
                                class="text-secondary fw-medium icon ion-md-contacts icon-md"
                            ></span>
                            <b> <i class="icon ion-md-clipboard"></i> {{ dp.dni }} </b>
                            <span
                                class="fw-medium text-primary pointer user-select-none"
                                mytooltip="Click para ver más información"
                                @click="handleGoAdvance(dp.dni)"
                            >
                                {{ dp.name }} {{ dp.lastname }}
                            </span>
                        </div>
                        <m-check
                            :id="`echeck-${dp.dni}`"
                            v-model="dp.checked"
                            text="Marcar como un estudiante diferente"
                        />
                    </li>
                </template>
            </ul>
        </FormTab>
        <DistrictModal @add-district="handleAddDistrict" />
    </section>
</template>
