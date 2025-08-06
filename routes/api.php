<?php

use App\Http\Controllers\UserController;
use App\Http\Requests\UserSignupRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->group(function () {
    Route::post('/user', 'create');
    Route::post('/login', 'login');
    Route::post('/user', 'get');
});

Route::get('/factory', function () {
    User::factory(5)->create();
});
