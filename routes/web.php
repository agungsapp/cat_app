<?php

use App\Livewire\Admin\BankSoalCreate;
use App\Livewire\Admin\BankSoalEdit;
use App\Livewire\Admin\BankSoalPage;
use App\Livewire\Admin\DashboardPage;
use App\Livewire\Admin\Master\JenisUjianPage;
use App\Livewire\Admin\Master\TipeUjianPage;
use App\Livewire\Peserta\PesertaDashboardIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->to('/admin/dashboard');
});

Route::prefix('admin/')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('dashboard', DashboardPage::class)->name('dashboard');
    Route::prefix('master/')->name('master.')->group(function () {
        Route::get('jenis-ujian', JenisUjianPage::class)->name('jenis-ujian');
        Route::get('tipe-ujian', TipeUjianPage::class)->name('tipe-ujian');
    });
    // bank soal
    Route::get('bank-soal', BankSoalPage::class)->name('bank-soal.index');
    Route::get('/bank-soal/create', BankSoalCreate::class)->name('bank-soal.create');
    Route::get('/bank-soal/{id}/edit', BankSoalEdit::class)->name('bank-soal.edit');
    // sesi ujian
    Route::get('/sesi-ujian', \App\Livewire\Admin\SesiUjianIndex::class)->name('sesi-ujian.index');
    Route::get('/sesi-ujian/create', \App\Livewire\Admin\SesiUjianCreate::class)->name('sesi-ujian.create');
    Route::get('/sesi-ujian/{id}/edit', \App\Livewire\Admin\SesiUjianEdit::class)->name('sesi-ujian.edit');
    Route::get('/sesi-ujian/{id}/assign', \App\Livewire\Admin\SesiUjianAssignSoal::class)->name('sesi-ujian.assign');
});

Route::prefix('peserta/')->name('peserta.')->middleware(['auth'])->group(
    function () {
        Route::get('dashboard', PesertaDashboardIndex::class)->name('dashboard.index');
    }
);

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Route::view('profile', 'profile')
//     ->middleware(['auth'])
//     ->name('profile');

require __DIR__ . '/auth.php';
