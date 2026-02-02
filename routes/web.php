<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    AttendanceController, AttendanceReportController, AuthController,
    DashboardController, GroupController, MemorizationController,
    StudentController, CourseController, StudentgroupController,
    UserController, TeacherReportController, CategoryController,
    ProfileController, QuranMemTestController, ReportController,
    StudentReportController
};

// --- الراوتات العامة ---
Route::get('/', fn() => Auth::check() ? redirect()->route('dashboard') : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// --- راوتات المسجلين دخول (أدمن ومحفظ) ---
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resources([
        'profile'      => ProfileController::class,
        'group'        => GroupController::class,
        'memorization' => MemorizationController::class,
    ]);

    Route::controller(AttendanceController::class)->group(function () {
        Route::get('/attendance', 'index')->name('attendance.index');
        Route::post('/attendance', 'store')->name('attendance.store');
    });

    // التقارير والـ AJAX والملفات الشخصية
    Route::controller(UserController::class)->group(function () {
        Route::get('/teachers/{id}/profile', 'show')->name('teachers.show');
        Route::get('/teachers/{id}/edit', 'edit')->name('teachers.edit');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/recitation', [ReportController::class, 'index'])->name('memorization');
        Route::get('/attendance', [AttendanceReportController::class, 'index'])->name('attendance');
        Route::get('/students', [StudentReportController::class, 'index'])->name('students');
        Route::get('/teachers-courses', [TeacherReportController::class, 'index'])->name('teachers_courses');
        Route::get('/get-filters-data', [ReportController::class, 'getFiltersData'])->name('filters.data');
        Route::get('/attendance-data', [AttendanceReportController::class, 'getAttendanceData'])->name('attendance.data');
    });

    // روابط AJAX المختصرة
    Route::controller(StudentReportController::class)->group(function () {
        Route::get('/get-groups-by-teacher/{teacherId}', 'getGroupsByTeacher')->name('get.groups.by.teacher');
        Route::get('/get-group-teacher/{groupId}', 'getGroupTeacher')->name('get.group.teacher');
    });

    // --- منطقة حماية المسؤول فقط ---
    Route::middleware('admin')->group(function () {
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
    });

    Route::get('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});
