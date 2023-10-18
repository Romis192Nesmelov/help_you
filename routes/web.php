<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\OrderController;
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
//Route::get('/map', [BaseController::class, 'map'])->name('map');
Route::get('/about', [BaseController::class, 'index'])->name('about');
Route::get('/how_does_it_work', [BaseController::class, 'index'])->name('how_does_it_work');
Route::get('/partners', [BaseController::class, 'partners'])->name('partners');

Route::prefix('auth')->name('auth.')->controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->name('login');
    Route::post('/generate-code', 'generateCode')->name('generate_code');
    Route::post('/register', 'register')->name('register');
    Route::post('/reset-password', 'resetPassword')->name('reset_password');
    Route::get('/logout', 'logout')->name('logout');
});

Route::prefix('account')->name('account.')->controller(AccountController::class)->middleware(['auth'])->group(function () {
    Route::get('/messages', 'messages')->name('messages');
    Route::get('/change', 'account')->name('change');
    Route::post('/get-code', 'getCode')->name('get_code');
    Route::post('/change-phone', 'changePhone')->name('change_phone');
    Route::post('/change-password', 'changePassword')->name('change_password');
    Route::post('/edit-account', 'editAccount')->name('edit_account');

    Route::get('/messages', 'messages')->name('messages');
    Route::get('/subscriptions', 'account')->name('subscriptions');
    Route::get('/my-requests', 'account')->name('my_requests');
    Route::get('/my-help', 'account')->name('my_help');
    Route::get('/incentives', 'account')->name('incentives');
});

Route::middleware(['auth','account.completed'])->controller(OrderController::class)->group(function () {
    Route::get('/new-order', 'newOrder')->name('new_order');
    Route::post('/next-step', 'nextStep')->name('next_step');
    Route::get('/next-step', 'nextStep')->name('next_step');
    Route::get('/prev-step', 'prevStep')->name('prev_step');
});
