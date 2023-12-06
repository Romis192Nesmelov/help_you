<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\OrderController;
use \App\Http\Controllers\ChatsController;
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

Route::controller(BaseController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/about', 'index')->name('about');
    Route::get('/how_does_it_work', 'index')->name('how_does_it_work');
    Route::get('/partners', 'partners')->name('partners');
    Route::get('/prev-url', 'prevUrl')->name('prev_url');
});

Route::prefix('auth')->name('auth.')->controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->name('login');
    Route::post('/generate-code', 'generateCode')->name('generate_code');
    Route::post('/register', 'register')->name('register');
    Route::post('/reset-password', 'resetPassword')->name('reset_password');
    Route::get('/logout', 'logout')->name('logout');
});

Route::prefix('account')->name('account.')->controller(AccountController::class)->middleware(['auth'])->group(function () {
    Route::post('/get-code', 'getCode')->name('get_code');
    Route::post('/change-phone', 'changePhone')->name('change_phone');
    Route::post('/change-password', 'changePassword')->name('change_password');
    Route::post('/edit-account', 'editAccount')->name('edit_account');
    Route::post('/delete-subscription', 'deleteSubscription')->name('delete_subscription');

    Route::get('/change', 'account')->name('change');
    Route::get('/messages', 'messages')->name('messages');
    Route::get('/my-subscriptions', 'mySubscriptions')->name('my_subscriptions');
    Route::get('/my-orders', 'myOrders')->name('my_orders');
    Route::get('/my-help', 'myHelp')->name('my_help');
    Route::get('/incentives', 'account')->name('incentives');
    Route::get('/subscription', 'subscription')->name('subscription');
});

Route::middleware(['auth','account.completed'])->name('order.')->controller(OrderController::class)->group(function () {
    Route::get('/new-order', 'newOrder')->name('new_order');
    Route::get('/orders', 'orders')->name('orders');
    Route::get('/edit-order', 'editOrder')->name('edit_order');
    Route::get('/read-order', 'readOrder')->name('read_order');
    Route::get('/get-subscriptions-news', 'getSubscriptionsNews')->name('get_subscriptions_news');

    Route::get('/get-user-age', 'getUserAge')->name('get_user_age');

    Route::post('/get-orders', 'getOrders')->name('get_orders');
    Route::post('/get-preview', 'getPreview')->name('get_preview');
    Route::post('/order-response', 'orderResponse')->name('order_response');

    Route::post('/next-step', 'nextStep')->name('next_step');
    Route::get('/prev-step', 'prevStep')->name('prev_step');

    Route::post('/delete-order', 'deleteOrder')->name('delete_order');
    Route::post('/delete-order-image', 'deleteOrderIMage')->name('delete_order_image');
    Route::post('/close-order', 'closeOrder')->name('close_order');
    Route::post('/resume-order', 'resumeOrder')->name('resume_order');
    Route::post('/delete-response', 'deleteResponse')->name('delete_response');
});

Route::middleware(['auth','account.completed'])->name('messages.')->controller(ChatsController::class)->group(function () {
    Route::get('/chats', 'chats')->name('chats');
    Route::get('/chat', 'chat')->name('chat');
    Route::get('/get-unread-messages', 'getUnreadMessages')->name('get_unread_messages');
    Route::post('/read-message', 'readMessage')->name('read_message');
    Route::post('/new-message', 'newMessage')->name('new_message');
});
