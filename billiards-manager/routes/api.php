<?php

// routes/api.php

use App\Http\Controllers\Api\ComboTimeController;
use Illuminate\Support\Facades\Route;

// ============ API ROUTES ============
// Bạn có thể dùng routes này nếu muốn separate API endpoints

Route::middleware('auth:sanctum')->group(function () {

    // ========== COMBO TIME SESSIONS API ==========
    Route::prefix('combo-sessions')->name('combo-sessions.')->group(function () {
        Route::post('/start', [ComboTimeController::class, 'start'])->name('start');
        Route::get('/{id}', [ComboTimeController::class, 'info'])->name('info');
        Route::post('/{id}/pause', [ComboTimeController::class, 'pause'])->name('pause');
        Route::post('/{id}/resume', [ComboTimeController::class, 'resume'])->name('resume');
        Route::post('/{id}/end', [ComboTimeController::class, 'end'])->name('end');
        Route::post('/{id}/add-minutes', [ComboTimeController::class, 'addMinutes'])->name('addMinutes');
        Route::get('/warnings', [ComboTimeController::class, 'checkWarnings'])->name('warnings');
        Route::get('/{id}/history', [ComboTimeController::class, 'history'])->name('history');
        Route::get('/report/daily', [ComboTimeController::class, 'dailyReport'])->name('dailyReport');
    });
});

// Public API endpoints (without auth if needed)
Route::prefix('public')->group(function () {
    // Add public API routes here
});

