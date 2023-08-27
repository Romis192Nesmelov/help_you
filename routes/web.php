<?php

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
Route::get('/about', [BaseController::class, 'index'])->name('about');
Route::get('/how_does_it_work', [BaseController::class, 'index'])->name('how_does_it_work');
Route::get('/for_partners', [BaseController::class, 'index'])->name('for_partners');
