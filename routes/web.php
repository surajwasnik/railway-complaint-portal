<?php

use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StationsController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;

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

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('voyager.dashboard');
    Route::post('/login', 'App\Http\Controllers\LoginController@login')->name('login');
    Route::post('complaints/import', [ComplaintController::class, 'import'])
        ->name('voyager.complaints.import');
});

Route::get('/stations', [StationsController::class, 'index'])->name('stations.index');

Route::get('/register', [UserController::class, 'create'])->name('user.register');
Route::post('/register', [UserController::class, 'store'])->name('user.store');
Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
Route::post('/user/{id}/update', [UserController::class, 'update'])->name('user.update');
