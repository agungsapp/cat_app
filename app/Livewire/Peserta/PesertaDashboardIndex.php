<?php

namespace App\Livewire\Peserta;

use App\Models\SesiUjian;
use App\Models\HasilUjian;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PesertaDashboardIndex extends Component
{
    public $ujianTersedia;
    public $ujianSelesai;
    public $rataRataNilai;
    public $nilaiTertinggi;

    public $ujianAktif = [];
    public $riwayatTerbaru = [];

    public function mount()
    {
        $this->loadStatistik();
        $this->loadUjianAktif();
        $this->loadRiwayatTerbaru();
    }

    public function loadStatistik()
    {
        $userId = Auth::id();

        // Ujian yang tersedia (aktif dan belum dikerjakan user ini)
        $this->ujianTersedia = SesiUjian::where('status', 'aktif')
            ->where('mulai', '<=', now())
            ->where('selesai', '>=', now())
            ->whereDoesntHave('hasilUjian', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->count();

        // Total ujian yang sudah selesai dikerjakan
        $this->ujianSelesai = HasilUjian::where('user_id', $userId)
            ->whereNotNull('selesai_at')
            ->count();

        // Rata-rata nilai peserta ini
        $this->rataRataNilai = HasilUjian::where('user_id', $userId)
            ->whereNotNull('selesai_at')
            ->whereNotNull('skor')
            ->avg('skor') ?? 0;

        // Nilai tertinggi peserta ini
        $this->nilaiTertinggi = HasilUjian::where('user_id', $userId)
            ->whereNotNull('selesai_at')
            ->whereNotNull('skor')
            ->max('skor') ?? 0;
    }

    public function loadUjianAktif()
    {
        $userId = Auth::id();

        // Ambil ujian yang sedang aktif dan belum dikerjakan
        $this->ujianAktif = SesiUjian::where('status', 'aktif')
            ->where('mulai', '<=', now())
            ->where('selesai', '>=', now())
            ->whereDoesntHave('hasilUjian', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->withCount('soal')
            ->latest('mulai')
            ->take(5)
            ->get();
    }

    public function loadRiwayatTerbaru()
    {
        // Ambil 5 hasil ujian terakhir
        $this->riwayatTerbaru = HasilUjian::where('user_id', Auth::id())
            ->with('sesiUjian')
            ->whereNotNull('selesai_at')
            ->latest('selesai_at')
            ->take(5)
            ->get();
    }

    public function refresh()
    {
        $this->loadStatistik();
        $this->loadUjianAktif();
        $this->loadRiwayatTerbaru();

        $this->dispatch('dashboard-refreshed');
    }

    public function render()
    {
        return view('livewire.peserta.peserta-dashboard-index');
    }
}
