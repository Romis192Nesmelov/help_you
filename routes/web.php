<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminUsersController;
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
    Route::get('/about', 'aboutUs')->name('about');

    Route::post('/feedback', 'feedback')->name('feedback');

    Route::get('/how_does_it_work/{slug?}', 'howDoesItWork')->name('how_does_it_work');
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
    Route::get('/get-news', 'getNews')->name('get_news');

    Route::post('/get-code', 'getCode')->name('get_code');
    Route::post('/change-phone', 'changePhone')->name('change_phone');
    Route::post('/change-password', 'changePassword')->name('change_password');
    Route::post('/change-avatar', 'changeAvatar')->name('change_avatar');
    Route::post('/edit-account', 'editAccount')->name('edit_account');
    Route::post('/delete-subscription', 'deleteSubscription')->name('delete_subscription');

    Route::get('/change', 'account')->name('change');
    Route::get('/messages', 'messages')->name('messages');

    Route::get('/my-subscriptions', 'mySubscriptions')->name('my_subscriptions');
    Route::get('/my-unread-subscriptions', 'getMyUnreadSubscriptions')->name('my_unread_subscriptions');

    Route::get('/my-orders', 'myOrders')->name('my_orders');
    Route::get('/set-read-unread-by-my-orders', 'setReadUnreadByMyOrders')->name('set_read_unread_by_my_orders');

    Route::get('/my-orders-active', 'myOrdersActive')->name('my_orders_active');
    Route::get('/my-orders-open', 'myOrdersOpen')->name('my_orders_open');
    Route::get('/my-orders-approving', 'myOrdersApproving')->name('my_orders_approving');
    Route::get('/my-orders-archive', 'myOrdersArchive')->name('my_orders_archive');

    Route::get('/my-help', 'myHelp')->name('my_help');
    Route::get('/set-read-unread-by-performer', 'setReadUnreadByPerformer')->name('set_read_unread_by_performer');

    Route::get('/my-help-active', 'myHelpActive')->name('my_help_active');
    Route::get('/my-help-archive', 'myHelpArchive')->name('my_help_archive');

    Route::get('/incentives', 'incentives')->name('incentives');
    Route::get('/get-my-incentive', 'getMyIncentives')->name('get_my_incentive');
    Route::post('/delete-incentive', 'deleteIncentive')->name('delete_incentive');

    Route::get('/subscription', 'subscription')->name('subscription');
});

Route::middleware(['auth','account.completed'])->name('order.')->controller(OrderController::class)->group(function () {
    Route::get('/new-order', 'newOrder')->name('new_order');
    Route::get('/orders', 'orders')->name('orders');
    Route::get('/edit-order', 'editOrder')->name('edit_order');
    Route::get('/read-order', 'readOrder')->name('read_order');

    Route::post('/remove-order-performer', 'removeOrderPerformer')->name('remove_order_performer');

    Route::post('/get-orders', 'getOrders')->name('get_orders');
    Route::post('/get-preview', 'getPreview')->name('get_preview');
    Route::post('/order-response', 'orderResponse')->name('order_response');

    Route::post('/next-step', 'nextStep')->name('next_step');
    Route::get('/prev-step', 'prevStep')->name('prev_step');

    Route::post('/delete-order', 'deleteOrder')->name('delete_order');
    Route::post('/delete-order-image', 'deleteOrderImage')->name('delete_order_image');
    Route::post('/close-order', 'closeOrder')->name('close_order');
    Route::post('/set-rating', 'setRating')->name('set_rating');
    Route::post('/resume-order', 'resumeOrder')->name('resume_order');
    Route::post('/delete-response', 'deleteResponse')->name('delete_response');
});

Route::middleware(['auth','account.completed'])->name('messages.')->controller(ChatsController::class)->group(function () {
    Route::get('/chats', 'chats')->name('chats');
    Route::get('/chats-my-orders', 'chatsMyOrders')->name('chats_my_orders');
    Route::get('/chats-performer', 'chatsPerformer')->name('chats_performer');
    Route::get('/chat', 'chat')->name('chat');
    Route::get('/get-unread-messages', 'getUnreadMessages')->name('get_unread_messages');
    Route::post('/read-message', 'readMessage')->name('read_message');
    Route::post('/new-message', 'newMessage')->name('new_message');
});

Route::controller(AdminLoginController::class)->prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', 'showLogin')->name('show_login');
    Route::post('/login', 'login')->name('login');
});

Route::prefix('admin')->middleware(['admin'])->name('admin.')->group(function () {
    Route::controller(AdminBaseController::class)->group(function () {
        Route::get('/', 'home')->name('home');
    });

    Route::controller(AdminUsersController::class)->group(function () {
        Route::get('/users/{slug?}', 'users')->name('users');
        Route::get('/get-users', 'getUsers')->name('get_users');
        Route::post('/change-avatar', 'changeAvatar')->name('change_avatar');
        Route::post('/edit-user', 'editUser')->name('edit_user');
//        Route::post('/delete-user', 'deleteUser')->name('delete_user');
    });
});
