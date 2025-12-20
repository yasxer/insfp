<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\SpecialtyController;

// Public routes (no authentication)
Route::get('/specialties', [SpecialtyController::class, 'index']);
Route::get('/specialties/{id}', [SpecialtyController::class, 'show']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {

    // Auth routes (all users)
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Student routes
    Route::middleware(['approved', 'role:student'])->prefix('student')->group(function () {
        Route::get('/dashboard', function () {
            return response()->json(['message' => 'Student Dashboard']);
        });
    });

    // Teacher routes
    Route::middleware(['approved', 'role:teacher'])->prefix('teacher')->group(function () {
        Route::get('/dashboard', function () {
            return response()->json(['message' => 'Teacher Dashboard']);
        });
    });

    // Administration routes
    Route::middleware(['approved', 'role:administration'])->prefix('admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard']);

        // Registration Numbers
        Route::post('/generate-registration', [AdminController::class, 'generateRegistrationNumber']);
        Route::get('/available-numbers', [AdminController::class, 'availableRegistrationNumbers']);
        Route::get('/used-numbers', [AdminController::class, 'usedRegistrationNumbers']);
        Route::delete('/registration-numbers/{id}', [AdminController::class, 'deleteRegistrationNumber']);

        // Student Management
        Route::get('/students', [AdminController::class, 'getStudents']);
        Route::get('/students/{id}', [AdminController::class, 'getStudent']);
        Route::put('/students/{id}', [AdminController::class, 'updateStudent']);
        Route::delete('/students/{id}', [AdminController::class, 'deleteStudent']);
        Route::get('/pending-registrations', [AdminController::class, 'pendingRegistrations']);
        Route::post('/students/{id}/approve', [AdminController::class, 'approveRegistration']);
        Route::post('/students/{id}/reject', [AdminController::class, 'rejectRegistration']);
        Route::post('/students/{id}/reset-password', [AdminController::class, 'resetStudentPassword']);

        // Teacher Management
        Route::get('/teachers', [AdminController::class, 'getTeachers']);
        Route::get('/teachers/{id}', [AdminController::class, 'getTeacher']);
        Route::post('/teachers', [AdminController::class, 'createTeacher']);
        Route::put('/teachers/{id}', [AdminController::class, 'updateTeacher']);
        Route::delete('/teachers/{id}', [AdminController::class, 'deleteTeacher']);
        Route::post('/teachers/{id}/reset-password', [AdminController::class, 'resetTeacherPassword']);

        // Specialty Management
        Route::get('/specialties', [AdminController::class, 'getSpecialties']);
        Route::get('/specialties/{id}', [AdminController::class, 'getSpecialty']);
        Route::post('/specialties', [AdminController::class, 'createSpecialty']);
        Route::put('/specialties/{id}', [AdminController::class, 'updateSpecialty']);
        Route::delete('/specialties/{id}', [AdminController::class, 'deleteSpecialty']);
        Route::post('/specialties/{id}/toggle-status', [AdminController::class, 'toggleSpecialtyStatus']);

        // Module Management
        Route::get('/modules', [AdminController::class, 'getModules']);
        Route::get('/modules/{id}', [AdminController::class, 'getModule']);
        Route::post('/modules', [AdminController::class, 'createModule']);
        Route::put('/modules/{id}', [AdminController::class, 'updateModule']);
        Route::delete('/modules/{id}', [AdminController::class, 'deleteModule']);
        Route::post('/modules/assign-teacher', [AdminController::class, 'assignTeacherToModule']);
        Route::post('/modules/remove-teacher', [AdminController::class, 'removeTeacherFromModule']);
    });
});
