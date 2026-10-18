<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Loaded within the authenticated, active, role-protected /admin route group.
Route::get('/', [AdminController::class, 'index'])->name('index');
Route::post('/assets', [AdminController::class, 'storeAssets'])->name('assets.store');
Route::delete('/assets/{asset}', [AdminController::class, 'destroyAsset'])->name('assets.destroy');
Route::post('/users/{user}/reset', [AdminController::class, 'sendReset'])->name('users.reset');
Route::get('/manage/{resource}', [AdminController::class, 'list'])->name('resources.index');
Route::get('/manage/{resource}/create', [AdminController::class, 'create'])->name('resources.create');
Route::post('/manage/{resource}', [AdminController::class, 'store'])->name('resources.store');
Route::get('/manage/{resource}/{id}/edit', [AdminController::class, 'edit'])->whereNumber('id')->name('resources.edit');
Route::put('/manage/{resource}/{id}', [AdminController::class, 'update'])->whereNumber('id')->name('resources.update');
Route::delete('/manage/{resource}/{id}', [AdminController::class, 'destroy'])->whereNumber('id')->name('resources.destroy');
