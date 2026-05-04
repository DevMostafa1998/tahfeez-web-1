<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MemorizationApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\MemorizationController;

Route::middleware('auth:sanctum')->group(function () {
    // مسار جلب البيانات
    Route::get('/sync-down', [MemorizationApiController::class, 'syncTeacherData']);
    // مسار رفع البيانات المسجلة Offline
    Route::post('/sync-up', [MemorizationApiController::class, 'syncUp']);
});
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/sync-memorizations', [MemorizationController::class, 'syncBulk']);
Route::get('/surahs', [MemorizationController::class, 'getSurahs']);
