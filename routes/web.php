<?php

use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StationsController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;
use App\Http\Controllers\Admin\HomeController;

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
    
    //Route::get('/', [HomeController::class, 'index'])->name('home');
    
    Route::post('complaints/import', [ComplaintController::class, 'import'])
        ->name('voyager.complaints.import');
    Route::get('/users', [UserController::class, 'index'])->name('voyager.users.index');
    Route::get('/admin/profile', function () {
        return redirect()->route('voyager.users.edit', auth()->id());
    })->name('voyager.profile');    
    Route::get('complaints/server-data', [ComplaintController::class, 'getServerData'])
        ->name('voyager.complaints.serverData');
        
    Voyager::routes();
        Route::get('/', [DashboardController::class, 'index'])->name('voyager.dashboard');
});


Route::get('/download-sample-csv', function () {
    $file = public_path('sample_csvs/complaints_sample.csv');
    return response()->download($file, 'complaints_sample.csv', [
        'Content-Type' => 'text/csv',
    ]);
});


Route::get('/stations', [StationsController::class, 'index'])->name('stations.index');

Route::get('/register', [UserController::class, 'create'])->name('user.register');
Route::post('/register', [UserController::class, 'store'])->name('user.store');
Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
Route::post('/user/{id}/update', [UserController::class, 'update'])->name('user.update');
Route::get('/complaints/{id}/edit', [ComplaintController::class, 'edit'])->name('complaints.edit');