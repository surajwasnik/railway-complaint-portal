<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ComplaintController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// Route::group(['prefix' => 'admin'], function () {
//     Voyager::routes();
// });
Route::get('/', function () {
    return redirect()->route('voyager.login');
})->name('login.show');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    Route::post('/login', 'App\Http\Controllers\LoginController@login')->name('login');
});
// Route::get('/webhook', [DashboardController::class, 'verifyWebhook']);
// //Route::post('/webhook', 'App\Http\Controllers\Admin\DashboardController@handleWebhook')->name('handleWebhook');;
// Route::post('/webhook', 'App\Http\Controllers\Admin\WebhookController@handleWebhook')->name('handleWebhook');;

//Route::post('/reset-password', 'App\Http\Controllers\ForgotPasswordController@submitResetPasswordForm')->name('reset.password.post');
