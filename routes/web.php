<?php

use App\Http\Controllers\AdminRegistrationController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\RegistrationController;
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
    Route::get('/registrations', [AdminRegistrationController::class, 'index'])->name('registrations.index');
    Route::get('/registrations/create', [AdminRegistrationController::class, 'create'])->name('registrations.create');
    Route::post('/registrations', [AdminRegistrationController::class, 'store'])->name('registrations.store');
    Route::patch('/registrations/{registration}', [AdminRegistrationController::class, 'update'])->name('registrations.update');
    Route::delete('/registrations/{registration}', [AdminRegistrationController::class, 'destroy'])->name('registrations.destroy');
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
