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

        // ✅ Ujian yang tersedia (aktif, dalam periode, dan belum exceed max_attempt)
        $this->ujianTersedia = SesiUjian::where('is_active', true)
            ->where('waktu_mulai', '<=', now())
            ->where('waktu_selesai', '>=', now())
            ->whereHas('tipeUjian')  // Pastikan ada tipe ujian
            ->where(function ($query) use ($userId) {
                $query->whereDoesntHave('hasilUjian', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                    ->orWhereHas('tipeUjian', function ($tq) use ($userId) {
                        // Cek max_attempt
                        $tq->whereRaw('
                        (tipe_ujians.max_attempt IS NULL) OR 
                        (SELECT COUNT(*) FROM hasil_ujians 
                         WHERE hasil_ujians.sesi_ujian_id = sesi_ujians.id 
                         AND hasil_ujians.user_id = ?) < tipe_ujians.max_attempt
                    ', [$userId]);
                    });
            })
            ->count();

        // Total ujian yang sudah selesai dikerjakan
        $this->ujianSelesai = HasilUjian::where('user_id', $userId)
            ->whereNotNull('selesai_at')
            ->count();

        // Rata-rata skor peserta
        $this->rataRataNilai = HasilUjian::where('user_id', $userId)
            ->whereNotNull('selesai_at')
            ->whereNotNull('skor')
            ->avg('skor') ?? 0;

        // Skor tertinggi peserta
        $this->nilaiTertinggi = HasilUjian::where('user_id', $userId)
            ->whereNotNull('selesai_at')
            ->whereNotNull('skor')
            ->max('skor') ?? 0;
    }

    public function loadUjianAktif()
    {
        $userId = Auth::id();

        // ✅ Eager load relasi + count soal via soal
        $this->ujianAktif = SesiUjian::where('is_active', true)
            ->where('waktu_mulai', '<=', now())
            ->where('waktu_selesai', '>=', now())
            ->with(['tipeUjian', 'soal'])  // ✅ Eager load
            ->where(function ($query) use ($userId) {
                $query->whereDoesntHave('hasilUjian', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                    ->orWhereHas('tipeUjian', function ($tq) use ($userId) {
                        $tq->whereRaw('
                        (tipe_ujians.max_attempt IS NULL) OR 
                        (SELECT COUNT(*) FROM hasil_ujians 
                         WHERE hasil_ujians.sesi_ujian_id = sesi_ujians.id 
                         AND hasil_ujians.user_id = ?) < tipe_ujians.max_attempt
                    ', [$userId]);
                    });
            })
            ->latest('waktu_mulai')
            ->take(5)
            ->get()
            ->map(function ($sesi) {
                // ✅ Tambah count soal manual dari soal
                $sesi->soal_count = $sesi->soal->count();
                return $sesi;
            });
    }

    public function loadRiwayatTerbaru()
    {
        // ✅ Eager load sesiUjian + tipeUjian
        $this->riwayatTerbaru = HasilUjian::where('user_id', Auth::id())
            ->with(['sesiUjian.tipeUjian'])
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
