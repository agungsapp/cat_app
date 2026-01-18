<?php

namespace App\Livewire\Peserta;

use App\Models\HasilUjian;
use Livewire\Component;

class PesertaUjianRiwayat extends Component
{
    public $riwayat;
    public $selectedHasil = null;
    public $soalList = [];
    public $jawaban = [];

    public function mount()
    {
        $this->riwayat = HasilUjian::with('sesiUjian.tipeUjian')
            ->where('user_id', auth()->id())
            ->latest('mulai_at')
            ->get();
    }

    public function lihatDetail($hasilId)
    {
        $this->selectedHasil = HasilUjian::with('sesiUjian.soal.opsi')->findOrFail($hasilId);

        $this->soalList = $this->selectedHasil->sesiUjian->soal()
            ->with(['opsi', 'jenis'])  // ✅ Tambah eager load jenisUjian
            ->get()
            ->groupBy('jenis_id')  // ✅ Group by jenis_id (kolom), bukan relasi
            ->map(fn($group) => $group->shuffle())
            ->flatten()
            ->values();

        $this->jawaban = $this->selectedHasil->jawaban()
            ->with('opsi')  // ✅ Eager load opsi untuk akses skor
            ->get()
            ->keyBy('soal_id');
    }

    public function render()
    {
        return view('livewire.peserta.peserta-ujian-riwayat');
    }
}
