<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', [AuthController::class, 'showRegistrationForm'])->name('show.registration');

Route::post('/register', [AuthController::class, 'register'])->name('register');
