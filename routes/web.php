<?php

use App\Http\Controllers\AccountController;
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

Route::get('/', [BaseController::class, 'index'])->name('home');
Route::get('/map', [BaseController::class, 'map'])->name('map');
Route::get('/about', [BaseController::class, 'index'])->name('about');
Route::get('/how_does_it_work', [BaseController::class, 'index'])->name('how_does_it_work');
Route::get('/partners', [BaseController::class, 'partners'])->name('partners');

Route::middleware(['auth'])->group(function () {
    Route::get('/messages', [AccountController::class, 'account'])->name('messages');
    Route::get('/account', [AccountController::class, 'account'])->name('account');
    Route::post('/get-code', [AccountController::class, 'getCode'])->name('get_code');
    Route::post('/change-phone', [AccountController::class, 'changePhone'])->name('change_phone');
    Route::post('/change-password', [AccountController::class, 'changePassword'])->name('change_password');
    Route::post('/edit-account', [AccountController::class, 'editAccount'])->name('edit_account');

    Route::get('/messages', [AccountController::class, 'messages'])->name('messages');
    Route::get('/subscriptions', [AccountController::class, 'account'])->name('subscriptions');
    Route::get('/my-requests', [AccountController::class, 'account'])->name('my_requests');
    Route::get('/my-help', [AccountController::class, 'account'])->name('my_help');
    Route::get('/incentives', [AccountController::class, 'account'])->name('incentives');
});

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/generate-code', [AuthController::class, 'generateCode'])->name('generate_code');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset_password');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
