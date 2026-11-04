<?php

use App\Http\Controllers\CompletionController;
use App\Http\Controllers\HabitController;
use App\Http\Middleware\LocalOnly;
use Illuminate\Support\Facades\Route;

Route::middleware([LocalOnly::class, 'throttle:120,1'])->group(function () {
    Route::get('/', [HabitController::class, 'index'])->name('habits.index');
    Route::resource('habits', HabitController::class)->except('index');
    Route::put('/habits/{habit}/completion', [CompletionController::class, 'store'])->name('completions.store');
    Route::delete('/habits/{habit}/completion', [CompletionController::class, 'destroy'])->name('completions.destroy');
});
