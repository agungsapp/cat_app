<?php

namespace App\Livewire\Peserta;

use Livewire\Component;
use App\Models\SesiUjian;
use App\Models\HasilUjian;
use Illuminate\Support\Facades\Auth;

class PesertaUjianKerjakan extends Component
{
    public $sesi;
    public $attempts;
    public $status;
    public $canStart = true; // Tambahan untuk disable button
    public $buttonText = 'Mulai / Lanjutkan';

    public function mount($sesi_id)
    {
        $this->sesi = SesiUjian::with('tipeUjian')->findOrFail($sesi_id);

        // Ambil semua attempt user
        $this->attempts = HasilUjian::where('user_id', Auth::id())
            ->where('sesi_ujian_id', $this->sesi->id)
            ->orderBy('id', 'desc')
            ->get();

        $this->buildStatusMessage();
    }

    private function buildStatusMessage()
    {
        $max = $this->sesi->tipeUjian->max_attempt;
        $count = $this->attempts->count();
        $hasOngoing = $this->attempts->whereNull('selesai_at')->first();

        // Cek jadwal dulu
        if (!$this->isWithinSchedule()) {
            $this->status = "<span class='text-red-600 font-semibold'>⚠️ Ujian ini belum dimulai atau sudah berakhir.</span>";
            $this->canStart = false;
            return;
        }

        // Cek status aktif
        if (!$this->sesi->is_active) {
            $this->status = "<span class='text-red-600 font-semibold'>⚠️ Sesi ujian ini sedang tidak aktif.</span>";
            $this->canStart = false;
            return;
        }

        // Jika max attempt ada & sudah penuh
        if ($max !== null && $count >= $max) {
            $this->status = "<span class='text-red-600 font-semibold'>❌ Anda telah mencapai batas maksimal percobaan ($max kali).</span>";
            $this->canStart = false;
            return;
        }

        // Jika masih ada yang belum selesai
        if ($hasOngoing) {
            $this->status = "<span class='text-blue-600 font-semibold'>📝 Anda memiliki ujian yang sedang berlangsung. Klik tombol untuk melanjutkan.</span>";
            $this->buttonText = 'Lanjutkan Ujian';
            $this->canStart = true;
            return;
        }

        // Jika belum pernah mulai
        if ($count === 0) {
            $this->status = "<span class='text-gray-700'>ℹ️ Anda belum pernah mengerjakan ujian ini. Klik tombol untuk memulai.</span>";
            $this->buttonText = 'Mulai Ujian';
            $this->canStart = true;
            return;
        }

        // Jika pernah selesai dan masih boleh mengulang
        if ($max === null || $count < $max) {
            $remaining = $max ? $max - $count : '∞';
            $this->status = "<span class='text-green-700'>✅ Anda dapat mengulang ujian ini. Sisa kesempatan: <b>$remaining</b> kali.</span>";
            $this->buttonText = 'Mulai Ulang Ujian';
            $this->canStart = true;
        }
    }

    public function start()
    {
        // Cek aktif + jadwal
        if (!$this->sesi->is_active) {
            session()->flash('error', 'Sesi ujian tidak sedang aktif.');
            return;
        }

        if (!$this->isWithinSchedule()) {
            session()->flash('error', 'Ujian belum dimulai atau sudah berakhir.');
            return;
        }

        $max = $this->sesi->tipeUjian->max_attempt;
        $count = $this->attempts->count();

        // Jika sudah max → block
        if ($max !== null && $count >= $max) {
            session()->flash('error', 'Anda telah mencapai batas maksimal percobaan.');
            return;
        }

        // Jika ada attempt yang belum selesai → lanjutkan
        $current = $this->attempts->whereNull('selesai_at')->first();
        if ($current) {
            return redirect()->route('peserta.ujian.soal', [
                'hasil_id' => $current->id,
                'nomor' => 1
            ]);
        }

        // Jika boleh mulai baru
        $baru = HasilUjian::create([
            'user_id' => Auth::id(),
            'sesi_ujian_id' => $this->sesi->id,
            'mulai_at' => now(),
        ]);

        return redirect()->route('peserta.ujian.soal', [
            'hasil_id' => $baru->id,
            'nomor' => 1
        ]);
    }

    private function isWithinSchedule()
    {
        $now = now();
        $start = $this->sesi->waktu_mulai;
        $end = $this->sesi->waktu_selesai;

        return (!$start || $start <= $now) && (!$end || $end >= $now);
    }

    public function render()
    {
        return view('livewire.peserta.peserta-ujian-kerjakan');
    }
}
