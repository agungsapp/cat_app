<?php

namespace App\Livewire\Peserta;

use App\Models\HasilUjian;
use App\Models\JawabanPeserta;
use App\Traits\HasAlert;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PesertaUjianSoal extends Component
{
    use HasAlert;

    public $hasilId;
    public $hasil;
    public $soalList;
    public $totalSoal = 0;
    public $nomor = 1;
    public $selectedOpsi = [];
    public $jawabanStatus = [];
    public $waktuSisa;

    public function mount($hasil_id)
    {
        $this->hasilId = $hasil_id;
        $this->hasil = HasilUjian::with(['sesiUjian.soal.opsi', 'sesiUjian.soal.jenis'])
            ->findOrFail($hasil_id);

        // Security check
        if ($this->hasil->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if already finished
        if ($this->hasil->selesai_at) {
            return redirect()->route('peserta.ujian.selesai', $this->hasilId);
        }

        $this->loadSoalList();
        $this->loadJawabanStatus();
        $this->updateWaktuSisa();
    }

    public function loadSoalList()
    {
        // Group by jenis_ujian_id, shuffle each group, then flatten
        $this->soalList = $this->hasil->sesiUjian->soal()
            ->with(['opsi', 'jenis'])
            ->get()
            ->groupBy('jenis_ujian_id')
            ->map(fn($group) => $group->shuffle())
            ->flatten()
            ->values();

        $this->totalSoal = $this->soalList->count();
    }

    public function loadJawabanStatus()
    {
        // Load existing answers
        $jawaban = JawabanPeserta::where('hasil_ujian_id', $this->hasilId)
            ->get()
            ->keyBy('soal_id');

        // Initialize status for all questions
        foreach ($this->soalList as $index => $soal) {
            $nomor = $index + 1;

            if (isset($jawaban[$soal->id])) {
                $this->selectedOpsi[$nomor] = $jawaban[$soal->id]->opsi_id;
                $this->jawabanStatus[$nomor] = 'terjawab';
            } else {
                $this->selectedOpsi[$nomor] = null;
                $this->jawabanStatus[$nomor] = 'belum';
            }
        }
    }

    public function pilihJawaban($nomor, $opsiId)
    {
        $soal = $this->soalList->get($nomor - 1);

        if (!$soal) {
            return;
        }

        $opsi = $soal->opsi->find($opsiId);

        if (!$opsi) {
            return;
        }

        $benar = $opsi->is_correct ?? false;

        try {
            // Save to database immediately
            JawabanPeserta::updateOrCreate(
                [
                    'hasil_ujian_id' => $this->hasilId,
                    'soal_id' => $soal->id
                ],
                [
                    'opsi_id' => $opsiId,
                    'benar' => $benar
                ]
            );

            // Update local state
            $this->selectedOpsi[$nomor] = $opsiId;
            $this->jawabanStatus[$nomor] = 'terjawab';

            // Success notification (subtle)
            $this->dispatch('jawaban-tersimpan');
        } catch (\Exception $e) {
            $this->alertError('Gagal Menyimpan', 'Terjadi kesalahan saat menyimpan jawaban.');
        }
    }

    public function pindahSoal($nomorBaru)
    {
        if ($nomorBaru >= 1 && $nomorBaru <= $this->totalSoal) {
            $this->nomor = $nomorBaru;
        }
    }

    public function updateWaktuSisa()
    {
        if (!$this->hasil->mulai_at) {
            $this->waktuSisa = 0;
            return;
        }

        $mulai = $this->hasil->mulai_at->timestamp;
        $durasi = $this->hasil->sesiUjian->durasi_menit * 60;
        $sekarang = now()->timestamp;

        $this->waktuSisa = max(0, $mulai + $durasi - $sekarang);

        // Auto finish when time is up
        if ($this->waktuSisa <= 0 && !$this->hasil->selesai_at) {
            $this->selesaiOtomatis();
        }
    }

    public function pollTimer()
    {
        $this->updateWaktuSisa();
    }

    public function konfirmasiSelesai()
    {
        $terjawab = collect($this->jawabanStatus)->filter(fn($s) => $s == 'terjawab')->count();
        $belum = $this->totalSoal - $terjawab;

        $text = "Anda telah menjawab {$terjawab} dari {$this->totalSoal} soal.";

        if ($belum > 0) {
            $text .= " Masih ada {$belum} soal yang belum dijawab.";
        }

        $text .= " Yakin ingin menyelesaikan ujian?";

        $this->alertConfirm(
            'Selesaikan Ujian?',
            $text,
            'selesai'
        );
    }

    public function selesai()
    {
        // Prevent double submission
        if ($this->hasil->selesai_at) {
            return redirect()->route('peserta.ujian.selesai', $this->hasilId);
        }

        $this->hitungSkor();

        $this->alertSuccess('Ujian Selesai!', 'Terima kasih telah mengerjakan ujian.');

        return redirect()->route('peserta.ujian.selesai', $this->hasilId);
    }

    public function selesaiOtomatis()
    {
        // Untuk waktu habis, langsung selesai tanpa konfirmasi
        if ($this->hasil->selesai_at) {
            return redirect()->route('peserta.ujian.selesai', $this->hasilId);
        }

        $this->hitungSkor();

        $this->alertWarning('Waktu Habis!', 'Ujian otomatis diselesaikan karena waktu habis.');

        return redirect()->route('peserta.ujian.selesai', $this->hasilId);
    }

    public function hitungSkor()
    {
        $jawabanBenar = JawabanPeserta::where('hasil_ujian_id', $this->hasilId)
            ->where('benar', true)
            ->with('soal') // pastikan relasi soal ada pada model
            ->get();
        $skorPeserta = $jawabanBenar->sum(function ($jawaban) {
            return $jawaban->soal->skor ?? 1;
        });
        $totalSkorMaks = $this->hasil->sesiUjian->soal->sum('skor');
        $nilai = 0;
        if ($totalSkorMaks > 0) {
            $nilai = ($skorPeserta / $totalSkorMaks) * 100;
        }
        $this->hasil->update([
            'selesai_at' => now(),
            'skor' => round($nilai, 2) // kalau mau dibuletin jadi integer bisa ganti ke round($nilai)
        ]);
    }


    public function render()
    {
        return view('livewire.peserta.peserta-ujian-soal');
    }
}
