<?php

namespace App\Livewire\Peserta;

use App\Models\HasilUjian;
use Livewire\Component;

class PesertaUjianTimer extends Component
{
    public $hasilId;
    public $waktuMulai;
    public $durasiMenit;
    public $waktuSisa;

    public function mount($hasil_id)
    {
        $this->hasilId = $hasil_id;
        $hasil = HasilUjian::with('sesiUjian')->findOrFail($hasil_id);

        if ($hasil->user_id !== auth()->id()) {
            abort(403);
        }

        $this->waktuMulai = $hasil->mulai_at->timestamp;
        $this->durasiMenit = $hasil->sesiUjian->durasi_menit;
        $this->waktuSisa = $this->durasiMenit * 60;
    }

    public function getListeners()
    {
        return [
            'echo:ujian-timer.' . $this->hasilId . ',UjianTimerTick' => 'updateTimer',
        ];
    }

    public function updateTimer($event)
    {
        $this->waktuSisa = $event['sisa'];
        if ($this->waktuSisa <= 0) {
            $this->dispatch('waktuHabis');
        }
    }

    public function render()
    {
        $menit = floor($this->waktuSisa / 60);
        $detik = $this->waktuSisa % 60;

        return view('livewire.peserta.peserta-ujian-timer', [
            'menit' => str_pad($menit, 2, '0', STR_PAD_LEFT),
            'detik' => str_pad($detik, 2, '0', STR_PAD_LEFT),
        ]);
    }
}
