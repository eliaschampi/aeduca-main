<script setup>
import { storeToRefs } from "pinia";
import { onMounted } from "vue";
import { useLogout } from "../../composables/utility";
import { useDegreeStore } from "../../pinia/degree";
import { useFamilyStore } from "../../pinia/family";
import { useStudentStore } from "../../pinia/student";
import { useTeacherStore } from "../../pinia/teacher";
import { useUserStore } from "../../pinia/user";
import MenuSide from "../components/MenuSide.vue";
import SideItem from "../components/SideItem.vue";
const { code: dc } = storeToRefs(useDegreeStore());
const { dni: tDni } = storeToRefs(useTeacherStore());
const { dni: sDni } = storeToRefs(useStudentStore());
const { dni: fDni } = storeToRefs(useFamilyStore());
const { can } = useUserStore();
const { showCash } = useLogout();

onMounted(() => {
    const sidebar = document.querySelector("#sidebar");
    const addActiveClass = (element) => {
        if (element.getAttribute("href") === location.pathname) {
            element.closest(".nav-item").classList.add("active");
            if (element.closest(".sub-menu")) {
                element.closest(".collapse").classList.add("show");
                element.classList.add("active");
            }
            if (element.closest(".submenu-item")) {
                element.classList.add("active");
            }
        }
    };

    const navLinks = sidebar.querySelectorAll(".nav li a");
    navLinks.forEach((link) => {
        addActiveClass(link);
    });

    sidebar.addEventListener("show.bs.collapse", function() {
        const openCollapses = sidebar.querySelectorAll(".collapse.show");
        openCollapses.forEach((collapse) => {
            collapse.classList.remove("show");
        });
    });

    const togglebtn = document.querySelector(".toggler");
    togglebtn.addEventListener("click", function() {
        const offcanvas = document.querySelector(".sidebar-offcanvas");
        offcanvas.classList.toggle("active");
        const isFloatingSidebar = window.innerWidth < 982;
        if (isFloatingSidebar) {
            const overlay = document.querySelector("#overlay");
            overlay.style.display = "block";
        }
    });
});
</script>
<template>
    <nav id="sidebar" class="sidebar sidebar-offcanvas">
        <ul class="nav">
            <li class="nav-item sidebar-category">
                <p>Sistema</p>
            </li>
            <li class="nav-item">
                <RouterLink class="nav-link" to="/">
                    <i class="icon ion-ios-apps menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </RouterLink>
            </li>
            <MenuSide id="sidesis" icon="icon ion-md-desktop" name="Sistema">
                <SideItem label="Acerca de" :to="{ name: 'about' }" />
                <SideItem :visible="can('A')" label="Configuración" :to="{ name: 'config' }" />
                <SideItem :visible="can('A')" label="Sedes" :to="{ name: 'branch' }" />
                <SideItem label="Usuarios" :to="{ name: 'main_user' }" />
                <SideItem :visible="can('A')" label="Monitoreo" :to="{ name: 'tracking' }" />
            </MenuSide>
            <li class="nav-item sidebar-category">
                <p>Registros Generales</p>
            </li>
            <template v-if="can('AS')">
                <MenuSide id="sidemain" icon="icon ion-ios-construct" name="Mantenimiento">
                    <SideItem label="Consultas avanzadas" :to="{ name: 'entitymanage' }" />
                    <SideItem :visible="can('A')" label="Cursos" :to="{ name: 'course' }" />
                    <SideItem :visible="can('A')" label="Clientes" :to="{ name: 'customer' }" />
                    <SideItem label="Tipos" :to="{ name: 'types' }" />
                </MenuSide>
            </template>
            <MenuSide id="sidedoc" icon="icon ion-ios-people" name="Docente">
                <SideItem
                    label="Perfil del docente"
                    :to="{
                        name: 'teacher_profile',
                        params: { dni: tDni },
                    }"
                />
                <SideItem label="Listado" :to="{ name: 'main_teacher' }" />
            </MenuSide>
            <MenuSide id="sidefam" icon="icon ion-md-people" name="Apoderado">
                <SideItem
                    label="Perfil del apoderado"
                    :to="{
                        name: 'family_profile',
                        params: { dni: fDni },
                    }"
                />
                <SideItem
                    label="Listado"
                    :to="{ name: 'main_family', params: { degree_code: dc } }"
                />
            </MenuSide>
            <MenuSide id="sidestu" icon="icon ion-md-person" name="Estudiante">
                <SideItem
                    label="Perfil del estudiante"
                    :to="{ name: 'student_profile', params: { dni: sDni } }"
                />
                <SideItem
                    :visible="can('AS')"
                    label="Registrar Nuevo"
                    :to="{ name: 'new_student' }"
                />
            </MenuSide>
            <li class="nav-item sidebar-category">
                <p>Administración</p>
            </li>
            <MenuSide id="sideadmi" icon="icon ion-md-folder" name="Académico">
                <SideItem
                    :visible="can('ANS')"
                    label="Resumen anual"
                    :to="{ name: 'main_cycle' }"
                />
                <SideItem
                    label="Sección"
                    :to="{
                        name: 'section_student',
                        params: { degree_code: dc },
                    }"
                />
                <SideItem
                    :visible="can('ANS')"
                    label="Horario"
                    :to="{
                        name: 'schedule',
                        params: { degree_code: dc },
                    }"
                />
                <SideItem
                    :visible="can('AS')"
                    label="Matrícula"
                    :to="{
                        name: 'register',
                        params: { dni: sDni },
                    }"
                />
            </MenuSide>
            <template v-if="can('AS')">
                <MenuSide id="sidePay" icon="icon ion-md-stats" name="Pagos">
                    <SideItem
                        label="Pagos del estudiante"
                        :to="{
                            name: 'payment',
                        }"
                    />
                    <SideItem
                        label="Reporte general"
                        :to="{ name: 'debts', params: { degree_code: dc } }"
                    />
                </MenuSide>
            </template>
            <template v-if="showCash">
                <MenuSide id="sidecash" icon="icon ion-md-cart" name="Caja">
                    <SideItem label="Caja" :to="{ name: 'cash' }" />
                    <SideItem label="Reporte mensual" :to="{ name: 'cashes' }" />
                    <SideItem label="Ingresos" :to="{ name: 'incomes' }" />
                    <SideItem label="Gastos" :to="{ name: 'expense' }" />
                    <SideItem label="Comprobantes Anulados" :to="{ name: 'canceleds' }" />
                </MenuSide>
            </template>
            <template v-if="can('APN')">
                <li class="nav-item sidebar-category">
                    <p>Coordinación</p>
                </li>
                <MenuSide
                    id="sideatt"
                    icon="icon ion-md-checkmark-circle-outline"
                    name="Asistencia"
                >
                    <SideItem label="Carnet" :to="{ name: 'carday' }" />
                    <SideItem label="Por sección" :to="{ name: 'main_attendance' }" />
                    <SideItem
                        label="Registro Manual"
                        :to="{ name: 'checkday', params: { degree_code: dc } }"
                    />
                    <SideItem label="Tardanzas" :to="{ name: 'absence' }" />
                    <SideItem label="Docentes" :to="{ name: 'teacher_attendance' }" />
                </MenuSide>
                <li class="nav-item sidebar-category">
                    <p>Psicopedagogía</p>
                </li>
                <MenuSide id="sideinc" icon="icon ion-md-text" name="Atenciones">
                    <SideItem label="Reporte general" :to="{ name: 'main_incidence' }" />
                    <SideItem label="Registrar nuevo" :to="{ name: 'new_incidence' }" />
                </MenuSide>
            </template>
        </ul>
    </nav>
</template>
