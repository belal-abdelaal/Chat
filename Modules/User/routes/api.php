<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;
use Modules\User\Models\User;

Route::controller(UserController::class)->group(function () {
    Route::post('/user', 'create');
    Route::post('/login', 'login');
    Route::get('/user', 'get');
});

Route::get('/factory', function () {
    User::factory(5)->create();
});
