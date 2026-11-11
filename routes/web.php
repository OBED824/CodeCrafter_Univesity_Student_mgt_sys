<?php

use App\Http\Controllers\AdminAcademicController;
use App\Http\Controllers\AdminRegistrationController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LecturerAcademicController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\StudentAcademicController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/contact', [ContactController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/contactlist', [ContactController::class, 'contactlist'])
    ->middleware(['auth', 'role:administrator'])
    ->name('contactlist');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:administrator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::resource('users', AdminUserController::class)->only(['index', 'create', 'store']);
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::get('/roles', [AdminRoleController::class, 'index'])->name('roles.index');
    Route::put('/roles/{role}', [AdminRoleController::class, 'update'])->name('roles.update');
    Route::get('/attendance', [AdminAcademicController::class, 'attendance'])->name('attendance.index');
    Route::patch('/attendance/{attendance}', [AdminAcademicController::class, 'updateAttendance'])->name('attendance.update');
    Route::get('/marks', [AdminAcademicController::class, 'marks'])->name('marks.index');
    Route::patch('/marks/{mark}', [AdminAcademicController::class, 'updateMark'])->name('marks.update');
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [AdminReportController::class, 'export'])->name('reports.export');
    Route::get('/registrations', [AdminRegistrationController::class, 'index'])->name('registrations.index');
    Route::get('/registrations/create', [AdminRegistrationController::class, 'create'])->name('registrations.create');
    Route::post('/registrations', [AdminRegistrationController::class, 'store'])->name('registrations.store');
    Route::patch('/registrations/{registration}', [AdminRegistrationController::class, 'update'])->name('registrations.update');
    Route::delete('/registrations/{registration}', [AdminRegistrationController::class, 'destroy'])->name('registrations.destroy');
    Route::resource('students', StudentController::class);
    Route::resource('courses', CourseController::class);
});

Route::middleware(['auth', 'role:lecturer'])->prefix('lecturer')->name('lecturer.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'lecturer'])->middleware('permission:view own courses')->name('dashboard');
    Route::get('/courses', [LecturerController::class, 'courses'])->middleware('permission:view own courses')->name('courses');
    Route::get('/courses/{course}/students', [LecturerController::class, 'students'])->middleware('permission:view course students')->name('courses.students');
    Route::get('/courses/{course}/assignments', [LecturerAcademicController::class, 'assignments'])->middleware('permission:view assignments')->name('courses.assignments');
    Route::post('/courses/{course}/assignments', [LecturerAcademicController::class, 'storeAssignment'])->middleware('permission:manage assignments')->name('courses.assignments.store');
    Route::get('/courses/{course}/notes', [LecturerAcademicController::class, 'notes'])->middleware('permission:view course notes')->name('courses.notes');
    Route::post('/courses/{course}/notes', [LecturerAcademicController::class, 'storeNote'])->middleware('permission:manage course notes')->name('courses.notes.store');
    Route::get('/courses/{course}/marks', [LecturerAcademicController::class, 'marks'])->middleware('permission:view course grades')->name('courses.marks');
    Route::post('/courses/{course}/marks', [LecturerAcademicController::class, 'storeMark'])->middleware('permission:manage grades')->name('courses.marks.store');
});

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'student'])->middleware('permission:view own registrations')->name('dashboard');
    Route::get('/courses', [RegistrationController::class, 'available'])->middleware('permission:register courses')->name('courses');
    Route::get('/registrations', [RegistrationController::class, 'mine'])->middleware('permission:view own registrations')->name('registrations.index');
    Route::get('/assignments', [StudentAcademicController::class, 'assignments'])->middleware('permission:view assignments')->name('assignments.index');
    Route::get('/marks', [StudentAcademicController::class, 'marks'])->middleware('permission:view own grades')->name('marks.index');
    Route::get('/notes', [StudentAcademicController::class, 'notes'])->middleware('permission:view course notes')->name('notes.index');
    Route::get('/attendance', [AttendanceController::class, 'index'])->middleware('permission:view own attendance')->name('attendance.index');
    Route::post('/courses/{course}/attendance', [AttendanceController::class, 'store'])->middleware('permission:record attendance')->name('attendance.store');
    Route::post('/courses/{course}/register', [RegistrationController::class, 'store'])->middleware('permission:register courses')->name('registrations.store');
    Route::delete('/courses/{course}/cancel', [RegistrationController::class, 'destroy'])->middleware('permission:register courses')->name('registrations.destroy');
});
