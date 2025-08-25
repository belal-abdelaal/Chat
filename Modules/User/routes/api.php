<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\User\Http\Controllers\UserController;
use Modules\User\Http\Middleware\ValidateToken;
use Modules\User\Models\User;

Route::prefix('/user')->controller(UserController::class)->group(function () {
    Route::post('/', 'create');
    Route::post('/login', 'login');
    Route::get('/', 'get');
    Route::put('/', 'update');
});

Route::get('/factory', function () {
    User::factory(5)->create();
});
