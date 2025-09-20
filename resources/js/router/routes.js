/**
 * @author Elias
 */
const routes = [
  {
    path: "/",
    component: () => import("../layout/Main.vue"),
    children: [
      {
        path: "",
        name: "home",
        component: () => import("../Views/Home.vue"),
        meta: {
          title: "Inicio",
          omitCheckYear: true,
        },
      },
      {
        path: "config",
        name: "config",
        component: () => import("../Views/System/Index.vue"),
        meta: {
          roles: "A",
          title: "Configuración",
        },
      },
      {
        path: "branches",
        name: "branch",
        component: () => import("../Views/System/Branch.vue"),
        meta: {
          roles: "A",
          title: "Sedes",
        },
      },
      {
        path: "tracking-user-activity",
        name: "tracking",
        component: () => import("../Views/System/Tracking.vue"),
        meta: {
          roles: "A",
          title: "Monitoreo de actividades",
        },
      },
      {
        path: "users",
        name: "main_user",
        component: () => import("../Views/User/Index.vue"),
        meta: {
          title: "Usuarios",
        },
      },
      {
        path: "new-user",
        name: "new_user",
        component: () => import("../Views/User/Create.vue"),
        meta: {
          roles: "A",
          title: "Registrar Usuario",
        },
      },
      {
        path: "update-user/:code",
        name: "update_user",
        component: () => import("../Views/User/Create.vue"),
        meta: {
          title: "Modificar Usuario",
        },
      },
      {
        path: "user",
        component: () => import("../layout/User.vue"),
        children: [
          {
            path: "profile",
            name: "user_profile",
            component: () => import("../Views/User/Info.vue"),
            meta: {
              title: "Mi perfil",
              omitCheckYear: true,
            },
          },
          {
            path: "password",
            name: "user_password",
            component: () => import("../Views/User/Password.vue"),
            meta: {
              title: "Cambiar contraseña",
            },
          },
          {
            path: "branch",
            name: "user_branch",
            component: () => import("../Views/User/Branch.vue"),
            meta: {
              title: "Sede",
              omitCheckYear: true,
            },
          },
          {
            path: "year",
            name: "user_year",
            component: () => import("../Views/User/UpdateYear.vue"),
            meta: {
              title: "Año Académico",
              omitCheckYear: true,
            },
          },
          {
            path: "history",
            name: "user_sign",
            component: () => import("../Views/User/SignHistory.vue"),
            meta: {
              title: "Sesiones",
            },
          },
        ],
      },
      {
        path: "entity-manager",
        name: "entitymanage",
        component: () => import("../Views/System/Entity.vue"),
        meta: {
          title: "Consultas avanzadas",
          roles: "AS",
        },
      },
      {
        path: "cursos",
        name: "course",
        component: () => import("../Views/Course/Index.vue"),
        meta: {
          roles: "A",
          title: "Registro de Cursos",
        },
      },
      {
        path: "types",
        name: "types",
        component: () => import("../Views/Types/Index.vue"),
        meta: {
          roles: "AS",
          title: "Tipos de Ingresos y Gastos",
        },
      },
      {
        path: "teacher-form/:dni?",
        name: "new_teacher",
        component: () => import("../Views/Person/Create.vue"),
        meta: {
          roles: "AS",
          title: "Docente",
        },
      },
      {
        path: "student-form/:dni?",
        name: "new_student",
        component: () => import("../Views/Person/Create.vue"),
        meta: {
          roles: "AS",
          title: "Estudiante",
        },
      },
      {
        path: "family-form/:dni?",
        name: "new_family",
        component: () => import("../Views/Person/Create.vue"),
        meta: {
          roles: "AS",
          title: "Apoderado",
        },
      },
      /**
       * 01 ESTUDIANTE
       */
      {
        path: "entity",
        component: () => import("../Views/Person/Container.vue"),
        children: [
          // main info
          {
            path: "estudiante",
            component: () => import("../Views/Student/Main.vue"),
            children: [
              {
                path: ":dni?/perfil",
                name: "student_profile",
                component: () => import("../Views/Student/Profile.vue"),
                meta: {
                  omitCheckYear: true,
                  title: "Perfil del Estudiante",
                },
              },
              {
                path: ":dni?/matricula",
                name: "register",
                component: () => import("../Views/Student/Register.vue"),
                meta: {
                  roles: "AS",
                  title: "Matrícula del Estudiante",
                },
              },
              {
                path: "register",
                component: () => import("../Views/Student/History.vue"),
                children: [
                  {
                    path: ":dni",
                    name: "es_register",
                    component: () => import("../Views/Student/RegStory.vue"),
                    meta: {
                      title: "Historial de matrículas",
                      omitCheckYear: true,
                    },
                  },
                  {
                    path: ":dni/family",
                    name: "es_family",
                    component: () => import("../Views/Student/Family.vue"),
                    meta: {
                      title: "Apoderados",
                    },
                  },
                  {
                    path: ":dni/info",
                    name: "es_doc",
                    component: () => import("../Views/Student/InfoStory.vue"),
                    meta: {
                      title: "Historial y documentos",
                      roles: "AS",
                    },
                  },
                  {
                    path: ":dni?/attendance",
                    component: () => import("../Views/Attendance/ByStudent.vue"),
                    name: "attendance_student",
                    meta: {
                      title: "Asistencia del estudiante",
                      roles: "AN",
                      omitCheckYear: true,
                    },
                  },
                  {
                    path: ":dni?/incidence",
                    component: () => import("../Views/Incidence/ByStudent.vue"),
                    name: "incidence_student",
                    meta: {
                      title: "Atenciones e incidencias",
                      roles: "ANP",
                      omitCheckYear: true,
                    },
                  },
                  {
                    path: ":dni?/justificaciones",
                    component: () => import("../Views/Attendance/Justification.vue"),
                    name: "justify_student",
                    meta: {
                      title: "Justificaciones",
                      omitCheckYear: true,
                      roles: "AN",
                    },
                  },
                ],
              },
            ],
          },

          // family
          {
            path: "family/:dni?",
            component: () => import("../Views/Family/Main.vue"),
            name: "family_profile",
            meta: {
              title: "Apoderado",
              ptype: "family",
            },
          },
          {
            path: "teacher",
            component: () => import("../Views/Teacher/Main.vue"),
            children: [
              {
                path: ":dni?/perfil",
                name: "teacher_profile",
                component: () => import("../Views/Teacher/Profile.vue"),
                meta: {
                  roles: "ASN",
                  title: "Docente",
                  ptype: "teacher",
                },
              },
              {
                path: ":dni?/asistencia",
                name: "t_attendance",
                component: () => import("../Views/Attendance/ByTeacher.vue"),
                meta: {
                  roles: "AN",
                  ptype: "teacher",
                  title: "Docente",
                  omitCheckYear: true,
                },
              },
              {
                path: ":dni?/horario",
                name: "t_schedule",
                component: () => import("../Views/Teacher/Schedule.vue"),
                meta: {
                  roles: "ASN",
                  ptype: "teacher",
                  title: "Docente",
                },
              },
            ],
          },
        ],
      },
      {
        path: "payment/:dni?",
        name: "payment",
        component: () => import("../Views/Payment/ByStudent.vue"),
        meta: {
          roles: "AS",
          title: "Pagos y Mensualidades",
        },
      },
      {
        path: "teachers",
        name: "main_teacher",
        component: () => import("../Views/Teacher/Index.vue"),
        meta: {
          roles: "ANS",
          title: "Módulo de Docentes",
        },
      },
      {
        path: "teacher-attendance",
        name: "teacher_attendance",
        component: () => import("../Views/Attendance/TeacherAttendance.vue"),
        meta: {
          roles: "AN",
          title: "Asistencia del docente",
          omitCheckYear: true,
        },
      },
      {
        path: "absences",
        name: "absence",
        component: () => import("../Views/Attendance/Absence.vue"),
        meta: {
          roles: "AN",
          title: "Tardanzas e Inasistencias",
        },
      },
      {
        path: "auto",
        name: "carday",
        component: () => import("../Views/Attendance/Automatically.vue"),
        meta: {
          roles: "AN",
          title: "Registrar Asistencia",
        },
      },
      /**
       * ciclo
       */
      {
        path: "list-cycles",
        name: "main_cycle",
        component: () => import("../Views/Cycle/Index.vue"),
        meta: {
          roles: "ANS",
          title: "Ciclos académicos",
          omitCheckYear: true,
        },
      },
      {
        path: "list-cycles/detail",
        name: "new_cycle",
        component: () => import("../Views/Cycle/Create.vue"),
        meta: {
          roles: "A",
          title: "Aperturar ciclo",
        },
      },
      /**
       * grado
       */
      {
        path: "degree",
        component: () => import("../layout/Degree.vue"),
        children: [
          {
            path: ":degree_code?/students",
            name: "section_student",
            component: () => import("../Views/Section/Student.vue"),
            meta: {
              title: "Estudiantes",
              omitCheckYear: true,
            },
          },
          {
            path: ":degree_code?/schedules",
            name: "schedule",
            component: () => import("../Views/Schedule/Schedule.vue"),
            meta: {
              title: "Horario",
              roles: "ANS",
              omitCheckYear: true,
            },
          },
          {
            path: ":degree_code?/att-by-section",
            name: "main_attendance",
            component: () => import("../Views/Attendance/StudentAttendance.vue"),
            meta: {
              roles: "AN",
              title: "Asistencia por sección",
              omitCheckYear: true,
            },
          },
          {
            path: ":degree_code?/register",
            name: "checkday",
            component: () => import("../Views/Attendance/Register.vue"),
            meta: {
              roles: "AN",
              title: "Asistencia del Estudiante",
            },
          },
          {
            path: ":degree_code?/family",
            name: "main_family",
            component: () => import("../Views/Family/Index.vue"),
            meta: {
              title: "Apoderados",
              omitCheckYear: true,
            },
          },
          {
            path: ":degree_code?/debts",
            name: "debts",
            component: () => import("../Views/Payment/Index.vue"),
            meta: {
              roles: "AS",
              title: "Pagos pendientes",
            },
          },
        ],
      },
      /**
       * incidencias
       */
      {
        path: "incidences",
        name: "main_incidence",
        component: () => import("../Views/Incidence/Index.vue"),
        meta: {
          roles: "ANP",
          title: "Módulo atenciones e incidencias",
          omitCheckYear: true,
        },
      },
      {
        path: "incidencias/create/:code?",
        name: "new_incidence",
        component: () => import("../Views/Incidence/Create.vue"),
        meta: {
          roles: "ANP",
          title: "Registro de Atencion",
        },
      },
      /**
       * Facturación
       */
      {
        path: "customer",
        name: "customer",
        component: () => import("../Views/Customer/Index.vue"),
        meta: {
          roles: "A",
          title: "Clientes",
        },
      },
      {
        path: "cash/history",
        name: "cashes",
        component: () => import("../Views/Cash/Cashes.vue"),
        meta: {
          roles: "AS",
          title: "Historial de Caja",
          omitCheckYear: true,
        },
      },
      {
        path: "income/create",
        name: "invoice",
        component: () => import("../Views/Income/Invoice.vue"),
        meta: {
          roles: "AS",
          title: "Ventas e Ingresos",
        },
      },
      {
        path: "incomes",
        name: "incomes",
        component: () => import("../Views/Income/Index.vue"),
        meta: {
          roles: "AS",
          title: "Ingresos de Hoy",
          omitCheckYear: true,
        },
      },
      {
        path: "canceleds",
        name: "canceleds",
        component: () => import("../Views/Income/Canceleds.vue"),
        meta: {
          roles: "AS",
          title: "Comprobantes Anulados",
          omitCheckYear: true,
        },
      },
      {
        path: "expense/create",
        name: "new_expense",
        component: () => import("../Views/Expense/Create.vue"),
        meta: {
          roles: "AS",
          title: "Registrar un Gasto",
        },
      },
      {
        path: "expense/update/:code",
        name: "update_expense",
        component: () => import("../Views/Expense/Create.vue"),
        meta: {
          roles: "AS",
          title: "Modificar Apoderado",
        },
      },
      {
        path: "espenses",
        name: "expense",
        component: () => import("../Views/Expense/Index.vue"),
        meta: {
          roles: "AS",
          title: "Gastos",
          omitCheckYear: true,
        },
      },
      {
        path: "caja",
        name: "cash",
        component: () => import("../Views/Cash/Cash.vue"),
        meta: {
          roles: "AS",
          title: "Caja",
        },
      },

      {
        path: "about",
        name: "about",
        component: () => import("../Views/System/About.vue"),
        meta: {
          title: "Acerca de",
          omitCheckYear: true,
        },
      },
      {
        path: "permission",
        name: "not_auth",
        component: () => import("../Views/Error/401.vue"),
        meta: {
          omitCheckYear: true,
        },
      },
    ],
  },
  {
    path: "/auth",
    component: () => import("../layout/Auth.vue"),
    children: [
      {
        path: "login",
        name: "login",
        component: () => import("../Views/Auth/Login.vue"),
      },
      {
        path: "recover",
        name: "recover",
        component: () => import("../Views/Auth/Recover.vue"),
      },
      {
        path: "/:pathMatch(.*)*",
        component: () => import("../Views/Error/404.vue"),
        meta: {
          omitCheckYear: true,
        },
      },
    ],
  },
];

export default routes;
