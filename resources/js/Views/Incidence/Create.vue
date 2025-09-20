<script setup>
import { computed, inject, onMounted, onUnmounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import File from "../../Components/Ui/File.vue";
import { tomorrow } from "../../core/date";
import day from "../../core/day.js";
import { downl } from "../../core/util";
import { downloadAttachedInApi, storeInApi, updateInApi } from "../../http";
import { useBranchStore } from "../../pinia/branch";
import { useIncidenceStore } from "../../pinia/incidence";
import { useModalStore } from "../../pinia/modal";
import { useSnackbarStore } from "../../pinia/snackbar";
import SimpleEditor from "../../Components/Ui/SimpleEditor.vue";
const emitter = inject("emitter");
const { successSnack, warningSnack } = useSnackbarStore();
const { hideModal } = useModalStore();
const isLoading = ref(false);
const entity_type = ref("student");
const router = useRouter();
const ptypes = {
    student: "Estudiante",
    teacher: "Docente",
    family: "Apoderado",
};

const incidence = reactive(useIncidenceStore().incidence);

const attached = ref(null);

const incidenceCode = useRoute().params.code;

const descriptionLabel = computed(() => {
    return `${1500 - incidence.description.length} caracteres restantes`;
});

const agreementLabel = computed(() => {
    return `(${500 - incidence.agreement.length} restantes)`;
});

function handleAddStudent({ name, lastname, dni }) {
    const exists = incidence.persons.some((item) => item.dni === dni);
    if (!exists) {
        incidence.persons.push({
            dni,
            name,
            lastname,
            entity_type: entity_type.value,
            actor_type: "involucrado",
        });
        hideModal("finderModal");
        entity_type.value = "student";
    } else {
        warningSnack("Ya esta agregado");
    }
}

function handleShowModal(who) {
    const { updateSfmOption } = useBranchStore();
    updateSfmOption({
        mode: 2,
        who,
        after: () => {
            entity_type.value = who;
            emitter.emit("showFinderModal");
        },
    });
}

function removeStudent(item) {
    incidence.persons.splice(incidence.persons.indexOf(item), 1);
}

function storeData() {
    if (incidenceCode) {
        return updateInApi(incidence);
    }
    const formData = new FormData();
    formData.append("data", JSON.stringify(incidence));
    formData.append("file", attached.value);
    return storeInApi(formData);
}

async function handleSave() {
    if (!incidence.persons.length) {
        warningSnack("Agregue estudiantes");
        return;
    }
    isLoading.value = true;
    const { data } = await storeData();
    successSnack(data.message);
    router.push({ name: "main_incidence" });
    isLoading.value = false;
}

async function handleDownloadClick() {
    const { data } = await downloadAttachedInApi(incidence.code);
    downl(data, incidence.file_attached, "");
}

const handleTypeChange = () => {
    incidence.extra = null;
    if (incidence.type === "pe") {
        incidence.agreement = "";
    }
};

onMounted(() => {
    emitter.on("afterSelectPerson", handleAddStudent);
    if (incidenceCode) {
        if (parseInt(incidenceCode) !== incidence.code) {
            router.push({ name: "main_incidence" });
            return;
        }
    }
    incidence.created_at = day(incidence.created_at).format("YYYY-MM-DDTHH:mm");
});

onUnmounted(() => {
    const { setIncidence } = useIncidenceStore();
    setIncidence();
    emitter.off("afterSelectPerson", handleAddStudent);
});

const schema = {
    title: {
        required_btw: [5, 100],
    },
    description: {
        required_btw: [10, 1500],
    },
    agreement: {
        required_btw: [10, 500],
    },
    created_at: {
        required: true,
        before: tomorrow(),
    },
    type: {
        required: true,
    },
};
</script>
<template>
    <m-form
        :is-loading="isLoading"
        :btn-name="incidenceCode ? 'Modificar' : 'Guardar'"
        title="Formulario de atención e incidencias"
        :schema="schema"
        @ok="handleSave"
    >
        <div class="row gx-2">
            <div class="col-md-6 space-b-1 mb-4 mb-md-0">
                <m-input v-model="incidence.title" name="title" label="Motivo (100 Caracteres)" />
                <SimpleEditor
                    v-model="incidence.description"
                    name="description"
                    :label="descriptionLabel"
                >
                    Desarrollo
                </SimpleEditor>
                <template v-if="incidence.type !== 'pe'">
                    <m-textarea v-model="incidence.agreement" name="agreement">
                        Conclusión y acuerdos
                        <i class="text-small"> {{ agreementLabel }} </i>
                    </m-textarea>
                </template>
                <template v-else>
                    <m-input
                        v-model="incidence.agreement"
                        name="agreement"
                        label="Fecha de permiso"
                        type="date"
                    />
                    <m-select
                        v-model="incidence.extra"
                        name="extra"
                        label="¿Por cuantos dias se registra el permiso?"
                        :disabled="!!incidenceCode"
                    >
                        <option :value="undefined" disabled selected hidden>Dias</option>
                        <option value="1">1 día</option>
                        <option value="2">2 días</option>
                        <option value="3">3 días</option>
                        <option value="4">4 días</option>
                        <option value="5">Una semana</option>
                    </m-select>
                    <alert class="font-italic">
                        Si se aprueba el permiso, su asistencia durante esos dias será actualizada
                        automáticamente
                    </alert>
                </template>
            </div>
            <div class="col-md-6 space-b-1">
                <m-input
                    v-model="incidence.created_at"
                    name="created_at"
                    type="datetime-local"
                    label="Fecha de incidencia"
                />
                <m-select
                    v-model="incidence.type"
                    name="type"
                    label="Tipo de atención"
                    @update:model-value="handleTypeChange"
                >
                    <option value="in">Incidencia médica</option>
                    <option value="co">Incidencia por conducta</option>
                    <option value="at">Atención</option>
                    <option value="re">Requisa</option>
                    <option value="pe">Permiso de asistencia</option>
                    <option value="ot">Otro</option>
                </m-select>
                <template v-if="!incidenceCode">
                    <File v-model="attached" />
                    <hr />
                    <h6 class="fw-medium">Agregar Involucrados</h6>
                    <m-button
                        size="btn-sm"
                        color="btn-inverse-info"
                        @ok="handleShowModal('student')"
                    >
                        Estudiante
                    </m-button>
                    <m-button
                        size="btn-sm"
                        color="btn-inverse-success"
                        @ok="handleShowModal('family')"
                    >
                        Apoderado
                    </m-button>
                    <m-button
                        size="btn-sm"
                        color="btn-inverse-success"
                        @ok="handleShowModal('teacher')"
                    >
                        Docente
                    </m-button>
                </template>
                <div v-else>
                    <p
                        v-show="incidence.file_attached"
                        class="text-secondary fw-bold pointer"
                        @click="handleDownloadClick"
                    >
                        <i class="icon ion-md-cloud-download icon-sm text-primary"></i>
                        Hay un archivo adjunto aquí.
                    </p>
                    <h6>Involucrados</h6>
                </div>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>#:</th>
                            <th>Nombre</th>
                            <th>Rol</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in incidence.persons" :key="index">
                            <th>{{ ptypes[item.entity_type] }}</th>
                            <th>
                                <span class="fw-medium">
                                    {{ `${item.name} ${item.lastname}` }}
                                </span>
                            </th>
                            <th>
                                <select
                                    v-model="item.actor_type"
                                    style="max-width: 7rem"
                                    class="form-select form-select-sm"
                                >
                                    <option value="involucrado">Involucrado</option>
                                    <option value="participante">Participante</option>
                                    <option value="testigo">Testigo</option>
                                    <option value="mediador">Mediador</option>
                                    <option value="informante">Informante</option>
                                    <option value="afectado">Afectado</option>
                                </select>
                            </th>
                            <th>
                                <span
                                    v-if="!incidenceCode"
                                    class="text-danger pointer"
                                    @click="removeStudent(item)"
                                >
                                    <i class="icon ion-md-remove-circle icon-md"></i>
                                </span>
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </m-form>
</template>
