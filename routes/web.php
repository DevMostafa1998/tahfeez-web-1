<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    AttendanceController,
    AttendanceReportController,
    AuthController,
    DashboardController,
    GroupController,
    MemorizationController,
    StudentController,
    CourseController,
    StudentgroupController,
    UserController,
    TeacherReportController,
    CategoryController,
    ProfileController,
    QuranMemTestController,
    ReportController,
    StudentReportController,
    ParentController,
    StudentPublicController,
    ExcelExportImportController,
    NotificationController,
    LovController,
    CenterController,
    CenterSelectionController,
    GenderSelectionController,
    MessageController,
};

// --- الراوتات العامة ---
Route::get('/', fn() => Auth::check() ? redirect()->route('dashboard') : redirect()->route('login'));
Route::get('/student_public', [StudentPublicController::class, 'index'])->name('student_public.index');
Route::post('/student_public', [StudentPublicController::class, 'store'])->name('student_public.store');
Route::get('user/restore/{id}', [App\Http\Controllers\UserController::class, 'restore'])->name('user.restore');
Route::get('/notifications/read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::get('/settings/exam-grades', [QuranMemTestController::class, 'editGradeSettings'])->name('settings.grades.edit');
Route::resource('lov', LovController::class)->except(['create', 'edit', 'show']);
Route::post('/settings/exam-grades/update', [QuranMemTestController::class, 'updateGradeSettings'])->name('settings.grades.update');
Route::get('/notifications/mark-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
Route::get('/centers', [CenterController::class, 'index'])->name('centers.index');
Route::post('/centers/store', [CenterController::class, 'store'])->name('centers.store');
Route::put('/centers/{id}', [CenterController::class, 'update'])->name('centers.update');
Route::delete('/centers/{id}', [CenterController::class, 'destroy'])->name('centers.destroy');
Route::prefix('excel-manage')->group(function () {

    Route::get('/{groupId}', [ExcelExportImportController::class, 'showExcelPage'])->name('excel.manage.page');

    // عمليات التصدير والاستيراد
    Route::get('/export/{groupId}', [ExcelExportImportController::class, 'exportExcel'])->name('excel.export');
    Route::post('/import', [ExcelExportImportController::class, 'importExcel'])->name('excel.import');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::get('/parent-login', function () {
    return view('auth.parent-login');
})->name('parent.login');
Route::get('student-data/{id}', [StudentController::class, 'getStudentData'])->name('student.data');
Route::get('student-courses/{id}', [StudentController::class, 'getStudentCourses'])->name('student.courses');
Route::get('/parents/{id_number?}', [ParentController::class, 'showStudentReport'])->name('parents.index');

Route::post('/parent/verify-student', [ParentController::class, 'verifyStudentPublic'])->name('parent.verify-student');
Route::get('/student_public/{id}/edit', [StudentController::class, 'editPublic'])->name('student_public.edit');
Route::put('/student_public/{id}', [StudentController::class, 'updatePublic'])->name('student_public.update');


// --- راوتات المسجلين دخول (لوحة التحكم) ---
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('memorization')->name('memorization.')->group(function () {
        Route::get('/student/{student_id}/edit-surahs', [MemorizationController::class, 'editStudentSurahs'])->name('edit_student_surahs');
        Route::post('/update-record/{id}', [MemorizationController::class, 'updateRecord'])->name('update_record');
    });

    Route::resources([
        'profile'      => ProfileController::class,
        'group'        => GroupController::class,
        'memorization' => MemorizationController::class,
    ]);
    
    Route::prefix('admin/messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/{teacherId}', [MessageController::class, 'index'])->name('chat');
        Route::get('/{teacherId}/data', [MessageController::class, 'chatData'])->name('data');
        Route::post('/send', [MessageController::class, 'store'])->name('send');
        Route::patch('/message/{id}', [MessageController::class, 'update'])->name('update');
        Route::delete('/message/{id}', [MessageController::class, 'destroy'])->name('destroy');
    });
    Route::controller(AttendanceController::class)->group(function () {
        Route::get('/attendance', 'index')->name('attendance.index');
        Route::post('/attendance', 'store')->name('attendance.store');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/recitation', [ReportController::class, 'index'])->name('memorization');
        Route::get('/reports/students/export', [StudentReportController::class, 'export'])->name('students.export');
        Route::get('/attendance', [AttendanceReportController::class, 'index'])->name('attendance');
        Route::get('/recitation-export', [ReportController::class, 'exportExcel'])->name('export');        // مسار جلب البيانات للـ DataTable (Server-side)
        Route::get('/attendance-data', [AttendanceReportController::class, 'getAttendanceData'])->name('attendance.data');
        Route::get('/get-filters-data', [ReportController::class, 'getFiltersData'])->name('filters.data');
        Route::get('/attendance-export', [AttendanceReportController::class, 'exportExcel'])->name('attendance.export');
        Route::get('/students', [StudentReportController::class, 'index'])->name('students');
        Route::get('/teachers-courses', [TeacherReportController::class, 'index'])->name('teachers_courses');
    });
    // روابط AJAX المختصرة
    Route::controller(StudentReportController::class)->group(function () {
        Route::get('/get-groups-by-teacher/{teacherId}', 'getGroupsByTeacher')->name('get.groups.by.teacher');
        Route::get('/get-group-teacher/{groupId}', 'getGroupTeacher')->name('get.group.teacher');
    });

    // --- منطقة حماية المسؤول (أدمن أو محفظ بصلاحيات) ---
    Route::middleware('admin')->group(function () {
        Route::get('user/{id}/edit-data', [UserController::class, 'getEditData'])->name('user.edit_data');
        Route::get('student-export-excel', [StudentController::class, 'exportExcel'])->name('student.export');
        Route::get('users/export-excel', [UserController::class, 'exportFullExcel'])->name('user.export.excel');
        Route::get('student-restore/{id}', [StudentController::class, 'restore'])->name('student.restore.get');
        Route::post('student/{id}/restore', [StudentController::class, 'restore'])->name('student.restore');
        Route::get('user/{id}/restore', [UserController::class, 'restore'])->name('user.restore');
        Route::resources([
            'user'         => UserController::class,
            'category'     => CategoryController::class,
            'student'      => StudentController::class,
            'studentgroup' => StudentgroupController::class,
            'courses'      => CourseController::class,
            'quran_tests'  => QuranMemTestController::class,
        ]);

        Route::controller(AttendanceController::class)->group(function () {
            Route::get('/teachers-attendance', 'teachersAttendance')->name('teachers.attendance');
            Route::post('/teachers-attendance', 'storeTeachersAttendance')->name('teachers.attendance.store');
        });

        Route::controller(UserController::class)->group(function () {
            Route::get('/teachers/{id}/profile', 'show')->name('teachers.show');
            Route::get('/teachers/{id}/edit', 'edit')->name('teachers.edit');
        });
    });

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
    Route::get('/quran-tests/get-student-teacher', [QuranMemTestController::class, 'getStudentTeacher'])
        ->name('quran_tests.student_teacher');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/send', [NotificationController::class, 'send'])->name('notifications.send');

    Route::post('/select-center', [CenterSelectionController::class, 'setCenter'])->name('center.select');
});
Route::post('/select-gender', [GenderSelectionController::class, 'setGender'])->name('gender.select');
