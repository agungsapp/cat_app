<?php

namespace App\Livewire\Peserta;

use App\Models\HasilUjian;
use Livewire\Component;

class PesertaUjianSelesai extends Component
{
    public $hasil;
    public $skor;
    public $totalSoal;
    public $jawabanBenar;
    public $durasiDigunakan;

    public function mount($hasil_id)
    {
        $this->hasil = HasilUjian::with(['sesiUjian', 'jawaban.soal'])->findOrFail($hasil_id);

        if ($this->hasil->user_id !== auth()->id()) {
            abort(403);
        }

        // Hitung ulang skor (pasti aman)
        $this->hitungSkor();

        // Durasi digunakan
        $totalMenit = $this->hasil->mulai_at->diffInMinutes($this->hasil->selesai_at ?? now());
        $durasiJamDesimal = $totalMenit / 60; // Ini yang menghasilkan 6.2666...
        $this->durasiDigunakan = ceil($durasiJamDesimal);
    }

    public function hitungSkor()
    {
        $this->jawabanBenar = $this->hasil->jawaban()->where('benar', true)->count();
        $skorPerSoal = $this->hasil->sesiUjian->soal->first()?->skor ?? 1;
        $this->skor = $this->jawabanBenar * $skorPerSoal;
        $this->totalSoal = $this->hasil->sesiUjian->soal->count();

        // Update jika belum
        if (!$this->hasil->skor) {
            $this->hasil->update([
                'selesai_at' => now(),
                'skor' => $this->skor
            ]);
        } else {
            $this->skor = $this->hasil->skor;
        }
    }

    public function render()
    {
        return view('livewire.peserta.peserta-ujian-selesai');
    }
}
