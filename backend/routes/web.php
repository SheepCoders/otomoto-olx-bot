<?php

use App\Http\Controllers\FilterController;

Route::get('/', [FilterController::class, 'create'])->name('filters.create');
Route::post('/filters', [FilterController::class, 'store'])->name('filters.store');
Route::get('/my-filters', [FilterController::class, 'showForm']);
Route::post('/my-filters', [FilterController::class, 'listFilters']);
Route::delete('/my-filters/{filter}', [FilterController::class, 'deleteFilter'])->name('filters.delete');