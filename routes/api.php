<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| BASE_API/api/users     -> Danh sách tất cả users
| BASE_API/api/users/{id} -> User theo id
*/

Route::get('/users', [UserController::class, 'apiIndex']);
Route::get('/users/{id}', [UserController::class, 'apiShow']);
