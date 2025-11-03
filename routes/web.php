<?php

use App\Livewire\Admin\DashboardPage;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::prefix('admin/')->name('admin.')->group(function () {
    Route::get('dashboard', DashboardPage::class);
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
