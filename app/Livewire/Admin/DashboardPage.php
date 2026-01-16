<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\SesiUjian;
use App\Models\HasilUjian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardPage extends Component
{
    public $totalPeserta;
    public $ujianAktif;
    public $ujianSelesaiHariIni;
    public $rataRataNilai;

    public $ujianBerlangsung = [];
    public $riwayatTerbaru = [];

    public function mount()
    {
        $this->loadStatistik();
        $this->loadUjianBerlangsung();
        $this->loadRiwayatTerbaru();
    }

    public function loadStatistik()
    {
        // Total Peserta
        $this->totalPeserta = User::where('role', 'peserta')->count();

        // Ujian Aktif (sesi yang sedang berjalan)
        $this->ujianAktif = SesiUjian::where('is_active', true)
            ->where('waktu_mulai', '<=', now())
            ->where('waktu_selesai', '>=', now())
            ->count();

        // Ujian Selesai Hari Ini
        $this->ujianSelesaiHariIni = HasilUjian::whereNotNull('selesai_at')
            ->whereDate('selesai_at', today())
            ->count();

        // Rata-rata Nilai (dari semua ujian yang selesai)
        $this->rataRataNilai = HasilUjian::whereNotNull('selesai_at')
            ->whereNotNull('skor')
            ->avg('skor') ?? 0;
    }

    public function loadUjianBerlangsung()
    {
        // Ujian yang sedang dikerjakan (mulai_at ada, selesai_at belum)
        $this->ujianBerlangsung = HasilUjian::with(['user', 'sesiUjian'])
            ->whereNotNull('mulai_at')
            ->whereNull('selesai_at')
            ->latest('mulai_at')
            ->take(5)
            ->get();
    }

    public function loadRiwayatTerbaru()
    {
        // 10 ujian terakhir yang sudah selesai
        $this->riwayatTerbaru = HasilUjian::with(['user', 'sesiUjian'])
            ->whereNotNull('selesai_at')
            ->latest('selesai_at')
            ->take(10)
            ->get();
    }

    public function refresh()
    {
        $this->loadStatistik();
        $this->loadUjianBerlangsung();
        $this->loadRiwayatTerbaru();

        $this->dispatch('dashboard-refreshed');
    }

    public function render()
    {
        return view('livewire.admin.dashboard-page');
    }
}
