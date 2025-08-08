<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CashActionTypeController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CycleController;
use App\Http\Controllers\DegreeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExtraInfoController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\IncidenceController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\IncomeDetailController;
use App\Http\Controllers\JustificationController;
use App\Http\Controllers\OpController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SignHistoryController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "auth"], function () {
    Route::post("login", [AuthController::class, "login"]);
    Route::put("recover", [AuthController::class, "recover"]);
    Route::post("refresh", [AuthController::class, "refresh"]);
});

Route::group(["middleware" => ["withkey"]], function () {

    //cedp p1
    Route::get("/attendance/{entity_identifier}/{from}/{to}/{priority}", [AttendanceController::class, "fetchByEntity"]);
    Route::get("/attendance_dw/{dni}/{from}/{to}/{priority}", [AttendanceController::class, "exportCV"]);
    Route::get("/payment/{register_code}", [PaymentController::class, "fetchByRegister"]);
    Route::get("/register_all/{dni}", [RegisterController::class, "fetchByStudent"]);
    Route::get("/incidence_print/{code}", [IncidenceController::class, "print"]);
    Route::get("/incidence_dw/{code}", [IncidenceController::class, "downloadAttached"]);
    Route::get("/cedp/incidence/{dni}", [IncidenceController::class, "fetchByEntity"]);

    // lesson
    Route::get("/lc/excel/{lesson_code}/{section_code}", [RegisterController::class, "fetchWithLCRbySC"]);
    Route::get("/lc/pdf/{lesson_code}/{register_code}", [RegisterController::class, "fetchWithLCRbyReg"]);
    Route::get("/lc/res/{section_code}/{register_code}", [RegisterController::class, "fetchForScoreReport"]);
    // config
    Route::get("/config", [ConfigController::class, "fetchConfig"]);
});

//group
Route::group(["middleware" => ["jwt.auth"]], function () {

    //mark
    Route::get("/counts", [SystemController::class, "counts"]);
    Route::post("/support", [SystemController::class, "sendMessageToElias"]);

    //auth
    Route::post("/logout", [AuthController::class, "logout"]);
    Route::get("/sign-history", [SignHistoryController::class, "index"]);
    Route::delete("/sign-history/{code}", [SignHistoryController::class, "destroyToken"]);
    Route::get("/tracking", [TrackingController::class, "index"]);
    Route::post("/check", [AuthController::class, "check"]);


    //cycle actives
    Route::get("/cycle", [CycleController::class, "fetchActives"]);
    Route::get("/cycle/{branch_code}", [CycleController::class, "fetchCycles"]);
    Route::get("/cycle_show/{code}", [CycleController::class, "show"]);
    Route::get("/cycle_code/{cycle_type}", [CycleController::class, "fetchLatestCode"]);
    Route::get("/cycle/v/{code}", [CycleController::class, "fetchAttVars"]);
    Route::post("/cycle", [CycleController::class, "store"]);
    Route::put("/cycle/{code}", [CycleController::class, "update"]);
    Route::put("/cycle/f/{code}", [CycleController::class, "finalize"]);
    //branch
    Route::get("/branch", [BranchController::class, "index"]);
    Route::post("/branch", [BranchController::class, "store"]);
    Route::put("/branch/{code}", [BranchController::class, "update"]);

    //courses y extras
    Route::resource("/curso", CourseController::class)->except(["create", "show", "edit"]);

    //config
    Route::get("/ctypes", [ConfigController::class, "fetchCycleTypes"]);
    Route::get("/company", [ConfigController::class, "fetchCompany"]);
    Route::put("/config/{key}", [ConfigController::class, "updateConfig"]);

    //system
    Route::get("/distritos", [SystemController::class, "ubigeo"]);
    Route::post("/soporte", [SystemController::class, "support"]);
    Route::post("/upload/bg", [SystemController::class, "uploadLogo"]);
    Route::get("/card/{id}/{type}", [SystemController::class, "printCard"]);

    //user
    Route::resource("/user", UserController::class)->except(["create", "edit"]);
    Route::get("/roles", [UserController::class, "roles"]);
    Route::put("/user/image/{code}", [UserController::class, "changeImage"]);
    Route::get("/user/{from}/{to}", [UserController::class, "fetchForCash"]);
    Route::put("/user_pass/{code}", [UserController::class, "changePassword"]);
    Route::put("/user_state/{code}", [UserController::class, "changeState"]);
    Route::put("/user_branch/{branch}", [UserController::class, "changeBranch"]);
    Route::put("/user_year", [UserController::class, "changeCurrentYear"]);

    //degree
    Route::get("/grados/{cycle_code}", [DegreeController::class, "index"]);
    Route::get("/grado/{code}", [DegreeController::class, "degree"]);

    //section
    Route::resource("/section", SectionController::class)->except(["edit", "show", "index"]);
    Route::get("/section/{cycle_code}", [SectionController::class, "fetchSumary"]);
    Route::get("/section_dg/{degree_code}", [SectionController::class, "fetchByDegree"]);

    //person
    Route::resource("/person", PersonController::class)->only(["show","update", "destroy"]);
    Route::get("/person/similars/{fullname}", [PersonController::class, "fetchSimilars"]);
    Route::get("/person/{type}/{dni}", [PersonController::class, "fetchByDNI"]);

    //profile
    Route::resource("/profile", ProfileController::class)->only(["store", "update", "destroy"]);
    Route::put("/profile/image/{code}", [ProfileController::class, "changeImage"]);
    Route::get("/profile_pdf/{dni}", [ProfileController::class, "printInfo"]);

    //register
    Route::get("/register/{dni}", [RegisterController::class, "fetchLastRegister"]);
    Route::get("/register/{section_code}/{inactives}", [RegisterController::class, "fetchBySection"]);
    Route::get("/register_has_cache", [RegisterController::class, "hasOnCache"]);
    Route::get("/register_p/{section_code}/{filterBy}", [RegisterController::class, "fetchDebts"]);
    Route::get("/register_etx/{section_code}", [RegisterController::class, "exportToExcel"]);
    Route::delete("/register/{code}", [RegisterController::class, "destroy"]);
    Route::post("/register", [RegisterController::class, "store"]);
    Route::put("/register/{code}", [RegisterController::class, "toggleState"]);

    //attendace
    Route::get("/register_asis/{section_code}/{priority}", [RegisterController::class, "fetchForAttendance"]);

    //teacher (usar index para imprimir)
    Route::resource("/teacher", TeacherController::class)->only(["show", "store", "update"]);
    Route::get("/teacher/search/{name}", [TeacherController::class, "search"]);
    Route::get("/teacher/self/{dni}", [TeacherController::class, "self"]);
    Route::get("/teacher/{spe}/{state}", [TeacherController::class, "fetchBySpe"]);
    Route::put("/teacher/state/{dni}", [TeacherController::class, "changeState"]);

    //student
    Route::resource("/student", StudentController::class)->only(["store", "show", "update"]);
    Route::get("/student/search/{branch_code}/{only_current_register}/{name}", [StudentController::class, "search"]);
    Route::put("/student/branch/{dni}", [StudentController::class, "updatebranch"]);
    Route::get("/student_pdf/{dni}", [StudentController::class, "printInfo"]);
    Route::get("/student_detail/{dni}", [StudentController::class, "duplicatedDetail"]);
    Route::get("/student_latest", [StudentController::class, "fetchLatest"]);

    // extrainfo
    Route::resource("/extrainfo", ExtraInfoController::class)->except(["index", "create"]);

    //apoderado
    Route::resource("/family", FamilyController::class)->only(["show", "store", "update"]);
    Route::get("/family/search/{name}", [FamilyController::class, "search"]);
    Route::get("/family/self/{dni}", [FamilyController::class, "self"]);
    Route::get("/family_sec/{section_code}", [FamilyController::class, "fetchBySection"]);

    //family_student
    Route::get("/family_s/{student_dni}", [FamilyController::class, "fetchByStudent"]);
    Route::get("/family_st/{family_dni}", [FamilyController::class, "fetchStudents"]);
    Route::put("/family_s", [FamilyController::class, "addStudent"]);
    Route::delete("/family_s/{family_dni}/{student_dni}", [FamilyController::class, "removeStudent"]);

    //op
    Route::resource("/op", OpController::class)->except(["index", "edit", "create"]);

    //schedule
    Route::resource("/schedule", ScheduleController::class)->only(["store", "update", "destroy"]);
    Route::get("/schedule/{section_code}", [ScheduleController::class, "fetchMain"]);
    Route::get("/schedule/teacher/{teacher_dni}", [ScheduleController::class, "fetchByTeacher"]);

    Route::resource("/customer", CustomerController::class)->except(["edit", "create", "show"]);
    Route::resource("/cat", CashActionTypeController::class)->only(["store", "show", "update"]);

    //payment
    Route::post("/payment", [PaymentController::class, "store"]);
    Route::put("/payment/{code}", [PaymentController::class, "update"]);
    Route::delete("/payment/{code}", [PaymentController::class, "destroy"]);
    Route::get("/paid/{register_code}", [IncomeDetailController::class, "fetchPaids"]);

    //cash
    Route::get("/cash/{user_code}/{date?}", [CashController::class, "fetch"]);
    Route::get("/cash_month/{month}", [CashController::class, "fetchByMonth"]);
    Route::get("/cash", [CashController::class, "fetchSimple"]);
    Route::get("/cash_last", [CashController::class, "lastCash"]);
    Route::get("/cash_export", [CashController::class, "exportToExcel"]);
    Route::get("/cash_chart", [CashController::class, "fetchChart"]);
    Route::post("/cash", [CashController::class, "openCash"]);
    Route::put("/cash/{code}", [CashController::class, "toggleCash"]);
    Route::put("/surrender/{code}", [CashController::class, "surrender"]);

    Route::get("/income/{from}/{to}/{user_code}", [IncomeController::class, "fetchByDates"]);
    Route::post("/income", [IncomeController::class, "store"]);
    Route::post("/income/pdf", [IncomeController::class, "printTicketPdf"]);
    Route::put("/income/{code}", [IncomeController::class, "canceled"]);
    Route::get("/income/canceled", [IncomeController::class, "canceleds"]);
    Route::get("/income_excel/{from}/{to}", [IncomeController::class, "exportToExcel"]);

    Route::get("/income_detail", [IncomeDetailController::class, "showFromCache"]);
    Route::get("/income_detail/{code}", [IncomeDetailController::class, "fetchByIncome"]);
    Route::put("/income_detail", [IncomeDetailController::class, "storeInCache"]);
    Route::delete("/income_detail", [IncomeDetailController::class, "cleanCachedIncome"]);
    Route::put("/income_detail/{id}", [IncomeDetailController::class, "removeItemFromCache"]);

    // expense
    Route::resource("/expense", ExpenseController::class)->except(["index", "create", "edit"]);
    Route::get("/expense/{from}/{to}/{user_code}", [ExpenseController::class, "fetchByDates"]);
    Route::get("/expense_print/{code}", [ExpenseController::class, "print"]);
    Route::get("/expense_export/{from}/{to}", [ExpenseController::class, "exportToExcel"]);

    //attendance
    Route::get("/attendance_t/{date}", [AttendanceController::class, "fetchForTeacherByDate"]);
    Route::get("/attendance/{section_code}/{date}/{priority}", [AttendanceController::class, "fetchBySection"]);
    Route::get("/attendance_sw/{section_code}/{date}/{priority}", [AttendanceController::class, "exportToExcelBySection"]);
    Route::get("/attendance_ab/{cycle_code}/{date}/{priority}", [AttendanceController::class, "fetchAbsences"]);
    Route::get("/attendance_chart", [AttendanceController::class, "fetchForChart"]);
    Route::post("/attendance", [AttendanceController::class, "store"]);
    Route::post("/attendance_auto", [AttendanceController::class, "auto"]);
    Route::put("/attendance/{code}", [AttendanceController::class, "update"]);

    //justification
    Route::get("/cedp/justification/{dni}", [JustificationController::class, "fetchByEntity"]);
    Route::get("/justification/{code}", [JustificationController::class, "downloadAttached"]);
    Route::post("/justification", [JustificationController::class, "store"]);
    Route::put("/justification/{code}/{aprove}", [JustificationController::class, "toggle"]);

    // incidence
    Route::get("/incidence/{month}", [IncidenceController::class, "fetchByMonth"]);
    Route::post("/incidence", [IncidenceController::class, "store"]);
    Route::put("/incidence/{code}", [IncidenceController::class, "update"]);
    Route::delete("/incidence/{code}", [IncidenceController::class, "destroy"]);
});
