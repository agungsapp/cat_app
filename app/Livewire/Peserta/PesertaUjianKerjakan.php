<?php

namespace App\Livewire\Peserta;

use App\Models\HasilUjian;
use App\Models\SesiUjian;
use Livewire\Component;

class PesertaUjianKerjakan extends Component
{
    public $sesiId;

    public function mount($sesi_id)
    {
        $this->sesiId = $sesi_id;

        $sesi = SesiUjian::findOrFail($sesi_id);

        // Cek jadwal
        if (!$sesi->is_active || !$this->isWithinSchedule($sesi)) {
            abort(403, 'Sesi tidak tersedia.');
        }

        // Buat atau ambil attempt
        $hasil = HasilUjian::firstOrCreate(
            ['user_id' => auth()->id(), 'sesi_ujian_id' => $sesi_id],
            ['mulai_at' => now()]
        );

        // Redirect ke soal pertama
        return redirect()->route('peserta.ujian.soal', [
            'hasil_id' => $hasil->id,
            'nomor' => 1
        ]);
    }

    private function isWithinSchedule($sesi)
    {
        $now = now();
        $start = $sesi->waktu_mulai;
        $end = $sesi->waktu_selesai;

        return (!$start || $start <= $now) && (!$end || $end >= $now);
    }

    public function render()
    {
        return view('livewire.peserta.peserta-ujian-kerjakan');
    }
}
