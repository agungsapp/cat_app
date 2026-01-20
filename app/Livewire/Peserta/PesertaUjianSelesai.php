<?php

namespace App\Livewire\Peserta;

use App\Models\HasilUjian;
use Livewire\Component;

class PesertaUjianSelesai extends Component
{
    public $hasil;
    public $skor;
    public $skorDetail;
    public $durasiDigunakan;

    public function mount($hasil_id)
    {
        $this->hasil = HasilUjian::with('sesiUjian')->findOrFail($hasil_id);

        // Security
        if ($this->hasil->user_id !== auth()->id()) {
            abort(403);
        }

        // 🚫 Jika belum selesai, tendang balik
        if (!$this->hasil->selesai_at) {
            return redirect()->route('peserta.ujian.soal', $this->hasil->id);
        }

        // ✅ Ambil skor FINAL (hasil hitung di PesertaUjianSoal)
        $this->skor = $this->hasil->skor;

        // ✅ Ambil breakdown skor (TWK / TIU / TKP)
        $this->skorDetail = $this->hasil->skor_detail
            ? json_decode($this->hasil->skor_detail, true)
            : [];

        // ✅ Durasi pengerjaan (menit)
        $durasi = $this->hasil->mulai_at
            ->diffInMinutes($this->hasil->selesai_at);

        $this->durasiDigunakan = max(1, $durasi);
    }

    public function render()
    {
        return view('livewire.peserta.peserta-ujian-selesai');
    }
}
