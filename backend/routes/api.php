<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\SpecialtyController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\TeacherAttendanceController;
use App\Http\Controllers\Api\TeacherGradesController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\AdminMessageController;
use App\Http\Controllers\Api\AdminDocumentController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\DocumentController;

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
        // Profile completion (accessible even if profile incomplete)
        Route::post('/complete-profile', [StudentController::class, 'completeProfile']);

        Route::get('/dashboard', [StudentController::class, 'dashboard']);
        Route::get('/profile', [StudentController::class, 'profile']);
        Route::put('/profile', [StudentController::class, 'updateProfile']);
        Route::put('/profile/password', [StudentController::class, 'updatePassword']);
        Route::get('/modules', [StudentController::class, 'modules']);
        Route::get('/grades', [StudentController::class, 'grades']);
        Route::get('/attendance', [StudentController::class, 'attendance']);
        Route::get('/schedule', [StudentController::class, 'schedule']);
        Route::get('/exams/results', [StudentController::class, 'examResults']);
        Route::get('/exams/upcoming', [StudentController::class, 'upcomingExams']);

        // Messages
        Route::get('/messages', [MessageController::class, 'index']);
        Route::get('/messages/unread/count', [MessageController::class, 'unreadCount']);
        Route::get('/messages/{id}', [MessageController::class, 'show']);

        // Lessons
        Route::get('/lessons/modules', [LessonController::class, 'modules']);
        Route::get('/lessons/modules/{moduleId}', [LessonController::class, 'moduleDetails']);
        Route::get('/lessons/{id}/download', [LessonController::class, 'download']);
        Route::get('/lessons/new/count', [LessonController::class, 'newCount']);

        // Documents
        Route::get('/documents', [DocumentController::class, 'index']);
        Route::get('/documents/{id}/download', [DocumentController::class, 'download']);
        Route::get('/documents/new/count', [DocumentController::class, 'newCount']);
    });

    // Teacher routes
    Route::middleware(['approved', 'role:teacher'])->prefix('teacher')->group(function () {
        Route::get('/dashboard', [TeacherController::class, 'dashboard']);
        Route::get('/profile', [TeacherController::class, 'profile']);
        Route::put('/profile', [TeacherController::class, 'updateProfile']);
        Route::get('/modules', [TeacherController::class, 'modules']);
        Route::get('/modules/{module}/students', [TeacherController::class, 'moduleStudents']);

        // Attendance Management
        Route::get('/attendance/sessions', [TeacherAttendanceController::class, 'sessions']);
        Route::get('/attendance/sessions/{schedule}/students', [TeacherAttendanceController::class, 'sessionStudents']);
        Route::post('/attendance', [TeacherAttendanceController::class, 'store']);
        Route::get('/attendance/history', [TeacherAttendanceController::class, 'history']);

        // Exam & Grades Management
        Route::get('/exams', [TeacherGradesController::class, 'exams']);
        Route::get('/exams/history', [TeacherGradesController::class, 'history']);
        Route::get('/exams/{exam}/students', [TeacherGradesController::class, 'examStudents']);
        Route::post('/exams/{exam}/results', [TeacherGradesController::class, 'storeResults']);
        Route::get('/schedule', [TeacherController::class, 'schedule']);
    });

    // Administration routes
    Route::middleware(['approved', 'role:administration'])->prefix('admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/statistics', [AdminController::class, 'statistics']);
        Route::get('/students/random', [AdminController::class, 'randomStudents']);
        Route::get('/charts/students-by-specialty', [AdminController::class, 'studentsBySpecialty']);
        Route::get('/charts/teachers-by-specialty', [AdminController::class, 'teachersBySpecialty']);

        // Registration Numbers
        Route::get('/registration/sessions', [AdminController::class, 'getRegistrationSessions']);
        Route::get('/registration/sessions/{session}/specialties', [AdminController::class, 'getRegistrationSessionSpecialties']);
        Route::get('/registration/list', [AdminController::class, 'getRegistrationNumbers']);
        Route::post('/generate-registration', [AdminController::class, 'generateRegistrationNumber']);
        Route::get('/available-numbers', [AdminController::class, 'availableRegistrationNumbers']);
        Route::get('/used-numbers', [AdminController::class, 'usedRegistrationNumbers']);
        Route::delete('/registration-numbers/{id}', [AdminController::class, 'deleteRegistrationNumber']);

        // Student Management
        Route::get('/students', [AdminController::class, 'getStudents']);
        Route::post('/students', [AdminController::class, 'createStudent']);
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
        Route::get('/teachers/{id}/schedule', [AdminController::class, 'getTeacherSchedule']);
        Route::post('/teachers', [AdminController::class, 'createTeacher']);
        Route::put('/teachers/{id}', [AdminController::class, 'updateTeacher']);
        Route::delete('/teachers/{id}', [AdminController::class, 'deleteTeacher']);
        Route::post('/teachers/{id}/reset-password', [AdminController::class, 'resetTeacherPassword']);

        // Specialty Management
        Route::get('/specialties', [SpecialtyController::class, 'index']);
        Route::get('/specialties/{id}', [SpecialtyController::class, 'show']);
        Route::post('/specialties', [SpecialtyController::class, 'store']);
        Route::put('/specialties/{id}', [SpecialtyController::class, 'update']);
        Route::delete('/specialties/{id}', [SpecialtyController::class, 'destroy']);
        // Route::post('/specialties/{id}/toggle-status', [AdminController::class, 'toggleSpecialtyStatus']);

        // Module Management
        Route::get('/modules', [AdminController::class, 'getModules']);
        Route::get('/modules/{id}', [AdminController::class, 'getModule']);
        Route::post('/modules', [AdminController::class, 'createModule']);
        Route::put('/modules/{id}', [AdminController::class, 'updateModule']);
        Route::delete('/modules/{id}', [AdminController::class, 'deleteModule']);
        Route::post('/modules/assign-teacher', [AdminController::class, 'assignTeacherToModule']);
        Route::post('/modules/remove-teacher', [AdminController::class, 'removeTeacherFromModule']);

        // Schedule Management
        Route::get('/schedules', [ScheduleController::class, 'index']);
        Route::get('/schedules/groups', [ScheduleController::class, 'getGroups']);
        Route::get('/schedules/specialty-semesters', [ScheduleController::class, 'getSpecialtySemesters']);
        Route::get('/schedules/{id}', [ScheduleController::class, 'show']);
        Route::post('/schedules', [ScheduleController::class, 'store']);
        Route::put('/schedules/{id}', [ScheduleController::class, 'update']);
        Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy']);

        // Message Management
        Route::post('/messages/send', [AdminMessageController::class, 'sendMessage']);
        Route::get('/messages', [AdminMessageController::class, 'index']);
        Route::get('/messages/stats', [AdminMessageController::class, 'stats']);

        // Document/File Management
        Route::get('/documents', [AdminDocumentController::class, 'index']);
        Route::post('/documents', [AdminDocumentController::class, 'store']);
        Route::delete('/documents/{id}', [AdminDocumentController::class, 'destroy']);
        Route::get('/documents/{id}/download', [AdminDocumentController::class, 'download']);
        Route::get('/documents/sessions', [AdminDocumentController::class, 'getSessions']);
        Route::get('/documents/sessions/{sessionId}/specialties', [AdminDocumentController::class, 'getSessionSpecialties']);

        // Session Management (Promotions)
        Route::get('/sessions', [SessionController::class, 'index']);
        Route::get('/sessions/archived', [SessionController::class, 'archived']);
        Route::get('/sessions/dropdown', [SessionController::class, 'getSessionsForDropdown']);
        Route::get('/sessions/specialties', [SessionController::class, 'getSpecialties']);
        Route::get('/sessions/study-types', [SessionController::class, 'getStudyTypes']);
        Route::get('/sessions/{id}', [SessionController::class, 'show']);
        Route::post('/sessions', [SessionController::class, 'store']);
        Route::put('/sessions/{id}', [SessionController::class, 'update']);
        Route::delete('/sessions/{id}', [SessionController::class, 'destroy']);
        Route::post('/sessions/{id}/specialties', [SessionController::class, 'addSpecialty']);
        Route::delete('/sessions/{sessionId}/specialties/{sessionSpecialtyId}', [SessionController::class, 'removeSpecialty']);
    });
});
