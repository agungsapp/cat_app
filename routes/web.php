<?php

use App\Livewire\Admin\DashboardPage;
use App\Livewire\Admin\Master\JenisUjianPage;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::prefix('admin/')->name('admin.')->group(function () {
    Route::get('dashboard', DashboardPage::class)->name('dashboard');
    Route::prefix('master/')->name('master.')->group(function () {
        Route::get('jenis-ujian', JenisUjianPage::class)->name('jenis-ujian');
    });
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
