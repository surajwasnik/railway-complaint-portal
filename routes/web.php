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
    Route::get('/complaints-details/{id}', [DashboardController::class, 'firDetails'])
        ->name('complaints.details');
});

