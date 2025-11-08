<?php

namespace App\Livewire\Peserta;

use App\Models\SesiUjian;
use App\Models\TipeUjian;
use Livewire\Component;

class PesertaUjianListIndex extends Component
{
    public $tipeUjian;
    public $slug;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->tipeUjian = TipeUjian::where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        $sesi = SesiUjian::where('tipe_ujian_id', $this->tipeUjian->id)
            ->where('is_active', true)
            // ->where(function ($q) {
            //     $q->whereNull('waktu_mulai')
            //         ->orWhere('waktu_mulai', '<=', now());
            // })
            // ->where(function ($q) {
            //     $q->whereNull('waktu_selesai')
            //         ->orWhere('waktu_selesai', '>=', now());
            // })
            ->latest()
            ->get();
        // return dd($sesi, $this->tipeUjian->id);

        return view('livewire.peserta.peserta-ujian-list-index', [
            'sesi' => $sesi,
            'tipeUjian' => $this->tipeUjian,
        ]);
    }
}
