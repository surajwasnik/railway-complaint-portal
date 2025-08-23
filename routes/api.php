<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/webhook', [DashboardController::class, 'verifyWebhook']);
Route::post('/webhook', [DashboardController::class, 'handleWebhook']);
Route::post('/refresh-token', [\App\Http\Controllers\Admin\DashboardController::class, 'generateRefreshToken']);