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

    // Chương (Chapter)
    Route::get('/chapters/subject/{mamonhoc}', [ChapterController::class, 'getBySubject'])->name('chapters.bySubject');
    Route::post('/chapters', [ChapterController::class, 'store'])->name('chapters.store');
    Route::put('/chapters/{id}', [ChapterController::class, 'update'])->name('chapters.update');
    Route::delete('/chapters/{id}', [ChapterController::class, 'destroy'])->name('chapters.destroy');
});

require __DIR__.'/auth.php';

