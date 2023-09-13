<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () { var_dump(23423423); })->name('home');
Route::get('/', [BaseController::class, 'index'])->name('home');
Route::get('/map', [BaseController::class, 'map'])->name('map');
Route::get('/about', [BaseController::class, 'index'])->name('about');
Route::get('/how_does_it_work', [BaseController::class, 'index'])->name('how_does_it_work');
Route::get('/for_partners', [BaseController::class, 'index'])->name('for_partners');
Route::get('/account', [BaseController::class, 'index'])->name('account');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/generate-code', [AuthController::class, 'generateCode'])->name('generate_code');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset_password');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
