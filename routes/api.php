<?php
use App\Http\Controllers\Admin\DashboardController;

Route::get('/webhook', [DashboardController::class, 'verifyWebhook']);
Route::post('/webhook', [DashboardController::class, 'handleWebhook']);
