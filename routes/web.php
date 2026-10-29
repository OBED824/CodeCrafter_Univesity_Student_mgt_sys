<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

// --- Public pages -----------------------------------------------------
Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/contact', [ContactController::class, 'contact'])->name('contact');
Route::get('/contactlist', [ContactController::class, 'contactlist'])->name('contactlist');
Route::post('/store', [ContactController::class, 'store'])->name('contact.store');

// --- Authentication ----------------------------------------------------
// Guests can only see login/register; authenticated users are redirected away.
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [HomeController::class, 'register'])->name('register');
});

// Everyone who is logged in can log out.
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')->name('logout');

// --- Role-protected areas ------------------------------------------------
// Each group is guarded by two layers: must be logged in AND must have the
// correct role. A student hitting /admin/students gets a 403 forbidden.

Route::middleware(['auth', 'role:administrator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::resource('students', StudentController::class);
    Route::resource('courses', CourseController::class);
});

Route::middleware(['auth', 'role:lecturer'])->prefix('lecturer')->name('lecturer.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'lecturer'])->name('dashboard');
    Route::get('/courses', [LecturerController::class, 'courses'])->name('courses');
    Route::get('/courses/{course}/students', [LecturerController::class, 'students'])->name('courses.students');
});

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'student'])->name('dashboard');
    Route::get('/courses', [RegistrationController::class, 'available'])->name('courses');
    Route::get('/registrations', [RegistrationController::class, 'mine'])->name('registrations.index');
    Route::post('/courses/{course}/register', [RegistrationController::class, 'store'])->name('registrations.store');
    Route::delete('/courses/{course}/cancel', [RegistrationController::class, 'destroy'])->name('registrations.destroy');
});
