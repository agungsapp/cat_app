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

    // Anti-Cheating Properties
    public $pelanggaranCount = 0;
    public $maxPelanggaran = 3; // Batas maksimal pelanggaran
    public $warningShown = false;

    public function mount($hasil_id)
    {
        $this->hasilId = $hasil_id;
        $this->hasil = HasilUjian::with(['sesiUjian.soal.opsi', 'sesiUjian.soal.jenis'])
            ->findOrFail($hasil_id);

        // Security check
        if ($this->hasil->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if already finished → Set flag, redirect di render()
        if ($this->hasil->selesai_at) {
            $this->redirectRoute('peserta.ujian.selesai', ['hasil_id' => $this->hasilId]);
            return;
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
        if (!$soal) return;

        $opsi = $soal->opsi->find($opsiId);
        if (!$opsi) return;

        // ✅ Tentukan 'benar' berdasarkan jenis
        $jenis = $soal->jenis;

        if ($jenis->tipe_penilaian === 'benar_salah') {
            $benar = $opsi->is_correct; // TWK/TIU
        } else {
            $benar = false; // TKP (ga ada konsep benar/salah)
        }

        try {
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

            $this->selectedOpsi[$nomor] = $opsiId;
            $this->jawabanStatus[$nomor] = 'terjawab';

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

    // Anti-Cheating: Catat pelanggaran
    public function catatPelanggaran()
    {
        // Prevent if already finished
        if ($this->hasil->selesai_at) {
            return;
        }

        $this->pelanggaranCount++;

        $sisaPelanggaran = $this->maxPelanggaran - $this->pelanggaranCount;

        if ($this->pelanggaranCount >= $this->maxPelanggaran) {
            // Batas tercapai → Auto selesai
            $this->selesaiKarenaPelanggaran();
        } else {
            // Tampilkan peringatan
            $this->dispatch('show-warning-pelanggaran', [
                'count' => $this->pelanggaranCount,
                'sisa' => $sisaPelanggaran
            ]);
        }
    }

    public function selesaiKarenaPelanggaran()
    {
        if ($this->hasil->selesai_at) {
            return redirect()->route('peserta.ujian.selesai', $this->hasilId);
        }

        $this->hitungSkor();

        $this->alertError(
            'Ujian Dihentikan!',
            'Ujian otomatis diselesaikan karena terlalu banyak meninggalkan halaman ujian.'
        );

        return redirect()->route('peserta.ujian.selesai', $this->hasilId);
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
        // Ambil semua jawaban peserta dengan relasi
        $jawaban = JawabanPeserta::where('hasil_ujian_id', $this->hasilId)
            ->with(['soal.jenis', 'opsi'])
            ->get();

        $skorPerJenis = [];
        $totalSkor = 0;

        foreach ($jawaban as $item) {
            $jenis = $item->soal->jenis;
            $jenisId = $jenis->id;

            // Initialize jika belum ada
            if (!isset($skorPerJenis[$jenisId])) {
                $skorPerJenis[$jenisId] = [
                    'nama' => $jenis->nama,
                    'kode' => $jenis->kode,
                    'skor' => 0,
                ];
            }

            // Hitung skor berdasarkan tipe penilaian
            if ($jenis->tipe_penilaian === 'benar_salah') {
                // TWK, TIU, SKD, SKB
                if ($item->benar) {
                    $skorSoal = $jenis->bobot_per_soal; // 5
                    $skorPerJenis[$jenisId]['skor'] += $skorSoal;
                    $totalSkor += $skorSoal;
                }
            } elseif ($jenis->tipe_penilaian === 'bobot_opsi') {
                // TKP
                if ($item->opsi) {
                    $skorSoal = $item->opsi->skor ?? 0; // 1-5
                    $skorPerJenis[$jenisId]['skor'] += $skorSoal;
                    $totalSkor += $skorSoal;
                }
            }
        }

        // Simpan ke hasil_ujian
        $this->hasil->update([
            'selesai_at' => now(),
            'skor' => $totalSkor,
            'skor_detail' => json_encode($skorPerJenis), // ✅ Simpan breakdown per jenis
        ]);
    }

    public function render()
    {
        return view('livewire.peserta.peserta-ujian-soal');
    }
}
