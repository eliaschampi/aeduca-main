<script setup>
import {
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Filler,
    Legend,
    LineElement,
    LinearScale,
    PointElement,
    Title,
    Tooltip,
} from "chart.js";
import { computed, onMounted, ref } from "vue";
import { Bar, Line } from "vue-chartjs";
import PulseLoader from "../Components/Ui/PulseLoader.vue";
import PersonLink from "../Components/Views/PersonLink.vue";
import cache from "../core/cache.js";
import buttons from "../data/buttons.json";
import { fetchChartCashApi, fetchCountsApi, fetchForChartAttApi, fetchLatestApi } from "../http";
import { useAppStore } from "../pinia/app";
import { useUserStore } from "../pinia/user";
ChartJS.register(
    Title,
    Tooltip,
    Legend,
    PointElement,
    BarElement,
    LinearScale,
    CategoryScale,
    LineElement,
    Filler,
);

const {
    fullyear,
    user: { name: username, rol },
} = useUserStore();

const app = useAppStore();
const isLoading = ref(false);

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    animation: false,
};
const months = ref([]);
const acuData = ref([]);
const surrData = ref([]);
const days = ref([]);
const dAtt = ref([]);
const counts = ref({});
const show_type = ref("admin");
const newSts = ref([]);

const dataAdmin1 = computed(() => ({
    labels: months.value,
    datasets: [
        {
            label: "Total acumulado",
            backgroundColor: "#f87979be",
            data: acuData.value,
        },
    ],
}));

const dataAdmin2 = computed(() => ({
    labels: months.value,
    datasets: [
        {
            label: "Total rendido del mes",
            backgroundColor: "#08BB79be",
            data: surrData.value,
            fill: "origin",
            tension: 0.1,
        },
    ],
}));

const dataAtt = computed(() => ({
    labels: days.value,
    datasets: [
        {
            label: "Estudiantes",
            backgroundColor: "#f8ce79be",
            data: dAtt.value,
            fill: "origin",
        },
    ],
}));

async function fetchCounts() {
    const { data } = await fetchCountsApi();
    counts.value = data;
    cache.setItem("counts", data);
}

async function fetchLatest() {
    const { data } = await fetchLatestApi();
    newSts.value = data.values;
}

async function fetchChartData() {
    const { data } = await fetchChartCashApi();
    months.value = data.months;
    acuData.value = data.acum;
    surrData.value = data.surr;
    setTimeout(() => {
        isLoading.value = false;
    }, 400);
}

function recalculateAttChart(data_a) {
    dAtt.value = data_a.count;
    days.value = data_a.days;
    setTimeout(() => {
        show_type.value = "psico";
        isLoading.value = false;
    }, 400);
}

async function fetchAttChartData() {
    const { data: data_a } = await fetchForChartAttApi();
    cache.setItem("data_att", {
        data_a,
    });
    recalculateAttChart(data_a);
}

function fetchOrRecalculateAttData() {
    const result = cache.getItem("data_att");
    if (result) {
        const { data_a } = result;
        recalculateAttChart(data_a);
    } else {
        fetchAttChartData();
    }
}
function handleReportTypeChange() {
    if (show_type.value === "admin") {
        isLoading.value = true;
        fetchOrRecalculateAttData();
    } else {
        show_type.value = "admin";
    }
}

onMounted(() => {
    isLoading.value = true;
    if (["A", "S"].indexOf(rol.code) !== -1) {
        show_type.value = "admin";
        fetchChartData();
    } else {
        fetchOrRecalculateAttData();
    }
    const countsCached = cache.getItem("counts");
    if (countsCached) {
        counts.value = countsCached;
    } else {
        fetchCounts();
    }
    fetchLatest();
});
</script>
<template>
    <section>
        <panel :title="`Hola ${username}!`">
            <b class="text-success">¡Bienvenido a {{ app.myapp }} {{ app.version }}</b>
            . Descubre nuestra nueva versión con importantes mejoras en seguridad, rendimiento y
            velocidad, además de una interfaz de usuario renovada para una experiencia más
            agradable.
            <div v-if="isLoading" style="float: right">
                <PulseLoader />
            </div>
            <hr />
            <div class="space">
                <template v-if="rol.code === 'A'">
                    <m-button
                        color="btn-inverse-success"
                        size="btn-sm"
                        @ok="handleReportTypeChange"
                    >
                        Ver
                        {{ show_type === "admin" ? "academicos" : "administrativos" }}
                    </m-button>
                </template>
                <m-button
                    v-show="show_type === 'psico'"
                    color="btn-inverse-primary"
                    size="btn-sm"
                    @ok="fetchAttChartData"
                >
                    Actualizar gráficos
                </m-button>
            </div>
        </panel>
        <div class="mygrid gcard mt-4">
            <div class="px-4 py-1 shadow-md myround">
                <div>
                    <div class="fw-medium">Estudiantes</div>
                    <h2 class="text-body opacity-75">{{ counts.s_count }}</h2>
                </div>
                <i class="icon ion-ios-contacts text-warning"></i>
            </div>
            <div class="px-4 py-1 shadow-md myround">
                <div>
                    <div class="fw-medium">Docentes</div>
                    <h2 class="text-body opacity-75">{{ counts.t_count }}</h2>
                </div>
                <i class="icon ion-ios-person text-secondary"></i>
            </div>
            <div class="px-4 py-1 shadow-md myround">
                <div>
                    <div class="fw-medium">Matriculas {{ fullyear }}</div>
                    <h2 class="text-body opacity-75">{{ counts.r_count }}</h2>
                </div>
                <i class="icon ion-md-stats text-success"></i>
            </div>
            <div class="px-4 py-1 shadow-md myround">
                <div>
                    <div class="fw-medium">Apoderados</div>
                    <h2 class="text-body opacity-75">{{ counts.f_count }}</h2>
                </div>
                <i class="icon ion-ios-people text-primary"></i>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <template v-if="show_type === 'admin'">
                    <div class="card card-body">
                        <h6 class="text-title fw-medium">
                            Gráfico acumulativo de ingresos rendidos
                        </h6>
                        <div class="chart-container">
                            <Bar
                                id="adminChart1"
                                :data="dataAdmin1"
                                :options="chartOptions"
                                height="300"
                            />
                        </div>
                    </div>
                    <div class="card card-body mt-4">
                        <h6 class="text-title fw-medium">Gráfico histórico de ingresos rendidos</h6>
                        <div class="chart-container">
                            <Line
                                id="adminChart2"
                                :data="dataAdmin2"
                                :options="chartOptions"
                                height="300"
                            />
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="card card-body">
                        <h6 class="text-title fw-medium">Gráfico de asistencia diaria</h6>
                        <small>
                            A partir del segundo trimestre anual, el grafico mostrará información
                            solo de los tres últimos meses
                        </small>
                        <div class="chart-container">
                            <Line
                                id="attChart1"
                                :data="dataAtt"
                                :options="chartOptions"
                                height="300"
                            />
                        </div>
                    </div>
                </template>
            </div>
            <div class="col-md-6 mt-4 mt-md-0">
                <div class="card card-body space-b-1">
                    <h6 class="text-title fw-medium">Area de {{ rol.name }}</h6>
                    <div class="opacity-50">
                        <b class="text-success">Puedes realizar:</b> {{ rol.description }}
                    </div>
                    <div class="space">
                        <m-router
                            :to="{ name: buttons[rol.code].b1.route }"
                            color="btn-inverse-info"
                            size="btn-sm"
                        >
                            {{ buttons[rol.code].b1.name }}
                        </m-router>
                        <m-router
                            :to="{ name: buttons[rol.code].b2.route }"
                            color="btn-inverse-success"
                            size="btn-sm"
                        >
                            {{ buttons[rol.code].b2.name }}
                        </m-router>
                        <m-router
                            :to="{ name: buttons[rol.code].b3.route }"
                            color="btn-inverse-danger"
                            size="btn-sm"
                        >
                            {{ buttons[rol.code].b3.name }}
                        </m-router>
                    </div>
                </div>
                <div class="card card-body mt-4">
                    <h6 class="text-title fw-medium">Ultimos 7 estudiantes registrados</h6>
                    <ul class="list-group list-group-flush">
                        <template v-for="item in newSts" :key="item.dni">
                            <li class="list-group-item d-flex align-items-center">
                                <img
                                    :src="`/default/${item.image || 'avatar.png'}`"
                                    alt="avatar"
                                    style="width: 2.5rem; height: 2.5rem,  borderRadius: '50%'"
                                />
                                <div class="ms-2 me-auto">
                                    <PersonLink :person="item" route="student_profile" />
                                    <div class="opacity-50 fst-italic">
                                        Creado el: {{ $datetim(item.created_at) }}
                                    </div>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</template>
