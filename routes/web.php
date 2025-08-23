<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Http\Controllers\StationsController;

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
});
// Route::group(['prefix' => 'admin', 'middleware' => ['web', 'admin.user']], function () {
//     Route::get('/stations', 'App\Http\Controllers\StationsController@index')->name('voyager.index');
//     // Route::get('/stations', [StationsController::class, 'statistics'])
//     //     ->name('voyager.stations.statistics');
// });
Route::get('/stations', 'App\Http\Controllers\Admin\StationsController@index')->name('stations.index');

Route::get('/register', [UserController::class, 'create'])->name('user.register');
Route::post('/register', [UserController::class, 'store'])->name('user.store');
Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
Route::post('/user/{id}/update', [UserController::class, 'update'])->name('user.update');
