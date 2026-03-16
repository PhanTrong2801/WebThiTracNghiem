<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Trang danh sách users (Frontend Inertia)
Route::get('/users', [UserController::class, 'index'])->name('users.index');

// API: Trả JSON - BASE_API/users/{id}
Route::get('/users/{id}', [UserController::class, 'apiShow']);

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Nhóm quyền (Roles)
    Route::resource('roles', RoleController::class)->except(['create', 'edit']);

    // Ngành/Khoa (Khoa)
    Route::resource('khoa', \App\Http\Controllers\KhoaController::class)->except(['create', 'edit', 'show']);

    // Môn học (Subject)
    Route::resource('subject', SubjectController::class)->except(['create', 'edit', 'show']);

    // Câu hỏi (Questions)
    Route::get('/questions', [\App\Http\Controllers\QuestionController::class, 'index'])->name('questions.index');
    Route::post('/questions', [\App\Http\Controllers\QuestionController::class, 'store'])->name('questions.store');
    Route::put('/questions/{id}', [\App\Http\Controllers\QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{id}', [\App\Http\Controllers\QuestionController::class, 'destroy'])->name('questions.destroy');

    // Chương (Chapter)
    Route::get('/chapters/subject/{mamonhoc}', [ChapterController::class, 'getBySubject'])->name('chapters.bySubject');
    // Chapters...

    // Nhóm học phần (Module) - Cho giảng viên/admin
    Route::get('/modules', [\App\Http\Controllers\ModuleController::class, 'index'])->name('modules.index');
    Route::post('/modules', [\App\Http\Controllers\ModuleController::class, 'store'])->name('modules.store');
    Route::put('/modules/{id}', [\App\Http\Controllers\ModuleController::class, 'update'])->name('modules.update');
    Route::delete('/modules/{id}', [\App\Http\Controllers\ModuleController::class, 'destroy'])->name('modules.destroy');
    Route::patch('/modules/{id}/visibility', [\App\Http\Controllers\ModuleController::class, 'toggleVisibility']);
    Route::patch('/modules/{id}/invite-code', [\App\Http\Controllers\ModuleController::class, 'refreshInviteCode']);
    Route::get('/modules/{id}/students', [\App\Http\Controllers\ModuleController::class, 'getStudents']);
    Route::post('/modules/{id}/students', [\App\Http\Controllers\ModuleController::class, 'addStudent']);
    Route::delete('/modules/{id}/students/{uid}', [\App\Http\Controllers\ModuleController::class, 'removeStudent']);

    // Phân công (Assignment) - Cho admin
    Route::get('/assignment', [\App\Http\Controllers\AssignmentController::class, 'index'])->name('assignment.index');
    Route::post('/assignment', [\App\Http\Controllers\AssignmentController::class, 'store'])->name('assignment.store');
    Route::get('/assignment/user/{uid}', [\App\Http\Controllers\AssignmentController::class, 'getByUser']);
    Route::delete('/assignment/{mamonhoc}/{uid}', [\App\Http\Controllers\AssignmentController::class, 'destroy'])->name('assignment.destroy');
    Route::delete('/assignment/user/{uid}', [\App\Http\Controllers\AssignmentController::class, 'destroyAll'])->name('assignment.destroyAll');

    // Học phần của sinh viên (Student Modules)
    Route::group(['prefix' => 'student'], function () {
        Route::get('/modules', [\App\Http\Controllers\StudentModuleController::class, 'index'])->name('student.modules.index');
        Route::post('/modules/join', [\App\Http\Controllers\StudentModuleController::class, 'join'])->name('student.modules.join');
        Route::delete('/modules/{id}/leave', [\App\Http\Controllers\StudentModuleController::class, 'leave'])->name('student.modules.leave');
        Route::patch('/modules/{id}/visibility', [\App\Http\Controllers\StudentModuleController::class, 'toggleVisibility'])->name('student.modules.visibility');
        Route::get('/modules/{id}/members', [\App\Http\Controllers\StudentModuleController::class, 'getMembers']);
    });
});

require __DIR__.'/auth.php';

