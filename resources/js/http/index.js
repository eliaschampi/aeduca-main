import cache from "../core/cache";
import api from "./axios";
import { ctForm, rblob } from "./common";

// app
export const fetchCompanyApi = () => api.get("/company");

export const fetchConfigApi = () => api.get("/config");
// main
export const fetchCyclesApi = () => api.get("/ctypes");

export const fetchActivesApi = () => api.get("/cycle");

export const updateConfigApi = (data, key) => api.put(`/config/${key}`, data);

export const sendToSupport = (data) => api.post("/support", data);

export const uploadConfigApi = (data) => api.post("/upload/bg", data, ctForm);

export const fetchUbiApi = () => api.get("/distritos");

export const fetchCountsApi = () => api.get("/counts");

export const printCardApi = (id, type) => api.get(`/card/${id}/${type}`, rblob);
// course

export const fetchAllCourses = () => api.get("/curso");

export const setCourse = (data) => api.post("/curso", data);

export const updateCourse = (data) => api.put(`/curso/${data.code}`, data);

export const delCourse = (code) => api.delete(`/curso/${code}`);

// attendance
export const fetchBySectionAttApi = ({ section_code, date, priority }) =>
  api.get(`/attendance/${section_code}/${date}/${priority}`);

export const fetchByEntityAttApi = ({ entity_identifier, from, to, priority }) =>
  api.get(`/attendance/${entity_identifier}/${from}/${to}/${priority}`);

export const fetchForTeacherAttApi = (date) => api.get(`/attendance_t/${date}`);

export const absencesAttApi = (cycle, date, pri) =>
  api.get(`/attendance_ab/${cycle}/${date}/${pri}`);

export const fetchForChartAttApi = () => api.get("/attendance_chart");

export const storeAttApi = (data) => api.post("/attendance", data);

export const updateAttApi = (data) => api.put(`/attendance/${data.code}`, data);

export const autoAttApi = (data) => api.post("/attendance_auto", data);

export const exportCVAttApi = (dni, from, to, priority) =>
  api.get(`/attendance_dw/${dni}/${from}/${to}/${priority}`, rblob);

export const exportToExcelAttApi = (section_code, date, priority) =>
  api.get(`/attendance_sw/${section_code}/${date}/${priority}`, rblob);

// branch
export const fetchAllBranchApi = () => api.get("/branch");

export const setBranchApi = (data) => api.post("/branch", data);

export const updateBranchApi = (data) => api.put(`/branch/${data.code}`, data);

// cash
export const fetchCashApi = (user_code, date) => api.get(`/cash/${user_code}/${date}`);

export const fetchSimpleCashApi = () => api.get("/cash");

export const fetchByMonthCashApi = (month) => api.get(`/cash_month/${month}`);

export const fetchLastCashApi = () => api.get("/cash_last");

export const fetchChartCashApi = () => api.get("/cash_chart");

export const exportToExcelCashApi = () => api.get("/cash_export", rblob);

export const openCashApi = (data) => api.post("/cash", data);

export const toggleCashApi = (code) => api.put(`/cash/${code}`);

export const surrenderCashApi = (data, cash_code) => api.put(`/surrender/${cash_code}`, data);

// cat
export const fetchCatApi = (mode) => api.get(`/cat/${mode}`);

export const storeCatApi = (data) => api.post("/cat", data);

export const updateCatApi = (data) => api.put(`/cat/${data.code}`, data);

// customer
export const fetchAllCrApi = () => api.get("/customer");

export const storeCrApi = (data) => api.post("/customer", data);

export const updateCrApi = (data) => api.put(`/customer/${data.code}`, data);

export const delCrApi = (code) => api.delete(`/customer/${code}`);

// cycle
export const fetchByCodeCyApi = (code) => api.get(`/cycle_show/${code}`);

export const fetchAllCyApi = (branch_code = 0) => api.get(`/cycle/${branch_code}`);

export const fetchCountByTypeCyApi = (type) => api.get(`/cycle_code/${type}`);

export const fetchAttVarsCyApi = (code) => api.get(`/cycle/v/${code}`);

export const setCyApi = (data) => api.post("/cycle", data);

export const updateCyApi = (data) => api.put(`/cycle/${data.code}`, data);

export const finalizeCyApi = (code) => api.put(`/cycle/f/${code}`);

// degree
export const fetchDgApi = (code) => api.get(`/grado/${code}`);

export const fetchAllDgApi = (code) => api.get(`/grados/${code}`);

//expense
export const fetchExApi = (code) => api.get(`/expense/${code}`);

export const fetchByDatesExApi = ({ from, to }, user_code) =>
  api.get(`/expense/${from}/${to}/${user_code}`);

export const exportToExcelExApi = (data) =>
  api.get(`/expense_export/${data.from}/${data.to}`, rblob);

export const storeExApi = (data) => api.post("/expense", data);

export const updateExApi = (data) => api.put(`/expense/${data.code}`, data);

export const delExApi = (code) => api.delete(`/expense/${code}`);

export const printExApi = (code) => api.get(`/expense_print/${code}`, rblob);

// extra
export const fetchByStExtraApi = (dni) => api.get(`/extrainfo/${dni}`);

export const storeExtraApi = (data) => api.post("/extrainfo", data, ctForm);

export const updateExtraApi = (data) => api.put(`/extrainfo/${data.code}`, data);

export const delExtraApi = (code) => api.delete(`/extrainfo/${code}`);

export const downloadAExtraApi = (code) => api.get(`/extrainfo/${code}/edit`, rblob);

//family
export const fetchFmApi = (dni) => api.get(`/family/self/${dni}`);

export const fetchBySectionFmApi = (section_code) => api.get(`/family_sec/${section_code}`);

export const fetchByStudentFmApi = (student_dni) => api.get(`/family_s/${student_dni}`);

export const addStudentFmApi = (data) => api.put("/family_s", data);

export const removeStudentFmApi = (family_dni, student_dni) =>
  api.delete(`/family_s/${family_dni}/${student_dni}`);

export const fetchStudentsFmApi = (family_dni) => api.get(`/family_st/${family_dni}`);

// incidence
export const fetchByMonthInApi = (month, page) => api.get(`/incidence/${month}?page=${page}`);

export const fetchByEntityInApi = (dni) => api.get(`/cedp/incidence/${dni}`);

export const storeInApi = (data) => api.post("/incidence", data, ctForm);

export const updateInApi = (data) => api.put(`/incidence/${data.code}`, data);

export const delInApi = (code) => api.delete(`/incidence/${code}`);

export const downloadAttachedInApi = (code) => api.get(`/incidence_dw/${code}`, rblob);

export const printInApi = (code) => api.get(`/incidence_print/${code}`, rblob);

// income
export const fetchByDateApi = ({ from, to }, user_code) =>
  api.get(`/income/${from}/${to}/${user_code}`);

export const fetchDetailApi = (code) => api.get(`/income_detail/${code}`);

export const fetchCanceledsApi = () => api.get("/income/canceled");

export const storeIncomeApi = (data) => api.post("/income", data);

export const printTicketPdfApi = (data) => api.post("/income/pdf", data, rblob);

export const canceledApi = (data) => api.put(`/income/${data.code}`, data);

export const fetchCachedDetailApi = () => api.get("/income_detail");

export const storeCachedDetailApi = (detail) => api.put("/income_detail", detail);

export const removeCachedDetailApi = (id) => api.put(`/income_detail/${id}`);

export const cleanCachedIncomeApi = () => api.delete("/income_detail");

export const exportToExcelIncomeApi = (data) =>
  api.get(`/income_excel/${data.from}/${data.to}`, rblob);

// justification
export const fetchByEntityJsApi = (dni) => api.get(`/cedp/justification/${dni}`);

export const downloadAttachedJsApi = (code) => api.get(`/justification/${code}`, rblob);

export const storeJsApi = (data) => api.post("/justification", data, ctForm);

export const toggleJsApi = (code, aprove) => api.put(`/justification/${code}/${aprove}`);

// op
export const fetchOpsApi = (section_code) => api.get(`/op/${section_code}`);

export const storeOpApi = (data) => api.post("/op", data);

export const updateOpApi = (code, data) => api.put(`/op/${code}`, data);

export const destroyOpApi = (code) => api.delete(`/op/${code}`);

// payment
export const fetchByRegisterPyApi = (register_code) => api.get(`/payment/${register_code}`);

export const fetchPaidsPyApi = (register_code) => api.get(`/paid/${register_code}`);

export const storePyApi = (data) => {
  if (!data.code) return api.post("/payment", data);
  return api.put(`/payment/${data.code}`, data);
};

export const destroyPyApi = (code) => api.delete(`/payment/${code}`);

//person
export const fetchPersonApi = (ptype, dni) => api.get(`/person/${ptype}/${dni}`);

export const delPersonApi = (dni) => api.delete(`/person/${dni}`);

export const changeDNIApi = (dni, newdni) => api.put(`/person/${dni}`, { newdni });

export const fetchSimilarsApi = (fullname) => api.get(`/person/similars/${fullname}`);

export const uploadProfileApi = (t, store_pid, formData) =>
  api.post(`/${t}/image/${store_pid}`, formData, ctForm);

//profile
export const storeProfileApi = (data) => api.post("/profile", data);

export const updateProfileApi = (dni) => api.put(`/profile/${dni}`);

export const destroyProfileApi = (dni) => api.delete(`/profile/${dni}`);

export const printProfileInfoApi = (dni) => api.get(`/profile_pdf/${dni}`, rblob);

// register
export const fetchRegistersApi = (dni) => api.get(`/register_all/${dni}`);

export const hasOnCacheRegApi = () => api.get("/register_has_cache");

export const fetchLatestRegApi = (dni) => api.get(`/register/${dni}`);

export const fetchDebtsRegApi = (section_code, filterBy) =>
  api.get(`/register_p/${section_code}/${filterBy}`);

export const exportToExcelRegApi = (section_code) =>
  api.get(`/register_etx/${section_code}`, rblob);

export const fetchForAttendanceApi = (code, priority) =>
  api.get(`/register_asis/${code}/${priority}`);

export const fetchBySectionRegApi = (s_code, inactives) =>
  api.get(`/register/${s_code}/${inactives}`);

export const fetchGroupedRegApi = () => api.get("/register_branch");

export const setRegApi = (data) => api.post("/register", data, data.consV ? rblob : {});

export const toggleStateRegApi = (code, state) => api.put(`/register/${code}`, { state });

export const delRegApi = (code) => api.delete(`/register/${code}`);

// schedule
export const fetchMainShApi = (section_code) => api.get(`/schedule/${section_code}`);

export const fetchByTeacherShApi = (teacher_dni) => api.get(`/schedule/teacher/${teacher_dni}`);

export const setShApi = (data) => api.post("/schedule", data);

export const updateShApi = (data) => api.put(`/schedule/${data.code}`, data);

export const delShApi = (code) => api.delete(`/schedule/${code}`);

//section
export const fetchSumaryApi = (cycle_code) => api.get(`/section/${cycle_code}`);

export const fetchByYearAndBranchApi = () => api.get("/section/create");

export const fetchByDegreeScApi = (code) => api.get(`/section_dg/${code}`);

export const setScApi = (data) => api.post("/section", data);

export const updateScApi = (data, section_code) => api.put(`/section/${section_code}`, data);

export const delScApi = (code) => api.delete(`/section/${code}`);

//student
export const fetchFullnameApi = (dni) => api.get(`/person/${dni}`);

export const printInfoStApi = (dni) => api.get(`/student_pdf/${dni}`, rblob);

export const changeBranchStApi = (dni, data) => api.put(`/student/branch/${dni}`, data);

export const fetchLatestApi = () => api.get("student_latest");

//teacher
export const fetchTeacherApi = (dni) => api.get(`/teacher/self/${dni}`);

export const showTeacherApi = (dni) => api.get(`/teacher/${dni}`);

export const fetchTeachersApi = (spe, state) => api.get(`/teacher/${spe}/${state}`);

export const changeStateTeacherApi = (dni) => api.put(`/teacher/state/${dni}`);

// user
export const fetchUserApi = (code) => api.get(`/user/${code}`);

export const fetchAllUserApi = () => api.get("/user");

export const cashersApi = (from, to) => api.get(`/user/${from}/${to}`);

export const fetchRolesUserApi = () => api.get("/roles");

export const setUserApi = (data) => api.post("/user", data);

export const updateUserApi = (data) => api.put(`/user/${data.code}`, data);

export const delUserApi = (code) => api.delete(`/user/${code}`);

export const changeBranchUserApi = (branch) => api.put(`/user_branch/${branch}`);

export const changeStateUserApi = (data) => api.put(`/user_state/${data.code}`, data);

export const changeCurrentYearUserApi = (year) => api.put("/user_year", { year });

export const changePasswordUserApi = (data) => api.put(`/user_pass/${data.user_code}`, data);

export async function logoutApi() {
  await api.post("/logout");
  cache.cleanAll();
  location.reload();
}

export const fetchSignHistoryApi = () => api.get("/sign-history");

export const removeSignHistoryApi = (code) => api.delete(`/sign-history/${code}`);

export const fetchTracking = (page) => api.get(`/tracking?page=${page}`);
