<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\UniversityController;
use App\Http\Controllers\VisaController; // ✅ Added Import

// ==========================================
// Default Home (Dashboard redirects by role)
// ==========================================
Route::get('/', [DashboardController::class, 'index'])->middleware('auth')->name('home');

// ==========================================
// Authentication Routes
// ==========================================
Auth::routes(['register' => true]);

// ==========================================
// SHARED ROUTES (Accessible by multiple roles)
// ==========================================
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [\App\Http\Controllers\StudentController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\StudentController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [\App\Http\Controllers\StudentController::class, 'update'])->name('profile.update');

    // Shared Application Routes
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/status', [ApplicationController::class, 'updateStatus'])->name('applications.updateStatus');

    // Chat Routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/fetch', [ChatController::class, 'fetch'])->name('chat.fetch');
    Route::post('/chat/send', [ChatController::class, 'store'])->name('chat.store');
});

// ==========================================
// STUDENT ROUTES
// ==========================================
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/dashboard', [DashboardController::class, 'student'])->name('student.dashboard');

    Route::resource('applications', ApplicationController::class)->except(['index', 'show']);
    Route::resource('files', FileController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('/universities', [\App\Http\Controllers\UniversityController::class, 'index'])->name('universities.index');
});

// ==========================================
// HR ROUTES
// ==========================================
Route::middleware(['auth', 'role:hr'])->group(function () {
    Route::get('/hr/dashboard', [DashboardController::class, 'hr'])->name('hr.dashboard');
    Route::resource('tasks', TaskController::class);
    Route::get('/hr/applications', [ApplicationController::class, 'index'])->name('hr.applications.index');
});

// ==========================================
// VISA CONSULTANT ROUTES (The One You Want!)
// ==========================================
Route::middleware(['auth', 'role:visa_consultant'])->group(function () {

    // ✅ This matches the redirect in DashboardController
    Route::get('/visa/dashboard', [VisaController::class, 'index'])->name('consultant.dashboard');

    Route::post('/visa/start/{application}', [VisaController::class, 'startProcess'])->name('visa.start');
    Route::get('/visa/tasks', [TaskController::class, 'index'])->name('consultant.tasks.index');
});

// ==========================================
// ADMIN ROUTES
// ==========================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::resource('students', StudentController::class);

    Route::get('admin/universities/import', [UniversityController::class, 'import'])->name('admin.universities.import');
    Route::post('admin/universities/import', [UniversityController::class, 'importSearch'])->name('admin.universities.search');
    Route::post('admin/universities/store-api', [UniversityController::class, 'storeApi'])->name('admin.universities.storeApi');
    Route::resource('admin/universities', UniversityController::class)->names('admin.universities');
});

// ==========================================
// ACADEMIC ADVISOR ROUTES
// ==========================================
Route::middleware(['auth', 'role:academic_advisor'])->group(function () {
    Route::get('/academic/dashboard', [DashboardController::class, 'academic'])->name('academic.dashboard');
});

// ==========================================
// 🌍 TRAVEL AGENT ROUTES
// ==========================================
Route::middleware(['auth', 'role:travel_agent'])->group(function () {

    // 1. Dashboard (The Queue)
    Route::get('/travel/dashboard', [\App\Http\Controllers\TravelController::class, 'index'])
        ->name('travel.dashboard');

    // 2. Start Booking Action
    Route::post('/travel/start/{application}', [\App\Http\Controllers\TravelController::class, 'startBooking'])
        ->name('travel.start');
});

// ==========================================
// API ROUTES
// ==========================================
Route::get('/api/orders', [DashboardController::class, 'getOrders'])->middleware('auth');
