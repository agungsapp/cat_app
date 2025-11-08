<?php

namespace App\Livewire\Peserta;

use App\Models\HasilUjian;
use App\Models\JawabanPeserta;
use App\Models\SesiUjian;
use Livewire\Component;

class PesertaUjianSoal extends Component
{
    public $hasilId;
    public $nomor;
    public $hasil;
    public $soal;
    public $soalList;
    public $selectedOpsi = null;
    public $totalSoal;

    public function mount($hasil_id, $nomor)
    {
        $this->hasilId = $hasil_id;
        $this->nomor = $nomor;

        $this->hasil = HasilUjian::with('sesiUjian.soal.opsi')->findOrFail($hasil_id);

        if ($this->hasil->user_id !== auth()->id()) {
            abort(403);
        }

        $this->loadSoalList();
        $this->loadCurrentSoal();
    }

    public function loadSoalList()
    {
        $this->soalList = $this->hasil->sesiUjian->soal()
            ->with('opsi')
            ->get()
            ->groupBy('jenis_ujian_id')
            ->map(fn($group) => $group->shuffle())
            ->flatten()
            ->values();

        $this->totalSoal = $this->soalList->count();
    }

    public function loadCurrentSoal()
    {
        if ($this->nomor < 1 || $this->nomor > $this->totalSoal) {
            return redirect()->route('peserta.ujian.selesai', $this->hasilId);
        }

        $this->soal = $this->soalList->get($this->nomor - 1);

        // Load jawaban sebelumnya
        $jawaban = JawabanPeserta::where('hasil_ujian_id', $this->hasilId)
            ->where('soal_id', $this->soal->id)
            ->first();

        $this->selectedOpsi = $jawaban?->opsi_id;
    }

    public function pilihJawaban($opsiId)
    {
        $this->selectedOpsi = $opsiId;

        $opsi = $this->soal->opsi->find($opsiId);
        $benar = $opsi?->is_correct ?? false;

        JawabanPeserta::updateOrCreate(
            ['hasil_ujian_id' => $this->hasilId, 'soal_id' => $this->soal->id],
            ['opsi_id' => $opsiId, 'benar' => $benar]
        );
    }

    public function next()
    {
        return redirect()->route('peserta.ujian.soal', [
            'hasil_id' => $this->hasilId,
            'nomor' => $this->nomor + 1
        ]);
    }

    public function prev()
    {
        return redirect()->route('peserta.ujian.soal', [
            'hasil_id' => $this->hasilId,
            'nomor' => $this->nomor - 1
        ]);
    }

    public function selesai()
    {
        $this->hitungSkor();
        return redirect()->route('peserta.ujian.selesai', $this->hasilId);
    }

    public function hitungSkor()
    {
        $benar = JawabanPeserta::where('hasil_ujian_id', $this->hasilId)->where('benar', true)->count();
        $skorPerSoal = $this->hasil->sesiUjian->soal->first()?->skor ?? 1;
        $totalSkor = $benar * $skorPerSoal;

        $this->hasil->update([
            'selesai_at' => now(),
            'skor' => $totalSkor
        ]);
    }

    public function render()
    {
        return view('livewire.peserta.peserta-ujian-soal');
    }
}
