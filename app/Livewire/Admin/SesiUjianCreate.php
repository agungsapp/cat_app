<?php

namespace App\Livewire\Admin;

use App\Models\SesiUjian;
use App\Traits\HasAlert;
use Livewire\Component;

class SesiUjianCreate extends Component
{
    use HasAlert;

    public $judul, $deskripsi, $tipe_ujian_id, $durasi_menit = 90;
    public $is_active = true;
    public $waktu_mulai, $waktu_selesai;

    protected $rules = [
        'judul' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'tipe_ujian_id' => 'required|exists:tipe_ujians,id',
        'durasi_menit' => 'required|integer|min:1',
        'waktu_mulai' => 'nullable|date',
        'waktu_selesai' => 'nullable|date|after:waktu_mulai',
    ];

    public function save()
    {
        $this->validate();

        SesiUjian::create([
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'tipe_ujian_id' => $this->tipe_ujian_id,
            'durasi_menit' => $this->durasi_menit,
            'is_active' => $this->is_active,
            'waktu_mulai' => $this->waktu_mulai,
            'waktu_selesai' => $this->waktu_selesai,
        ]);

        $this->alertSuccess('Berhasil', 'Sesi ujian dibuat!');
        return redirect()->route('admin.sesi-ujian.index');
    }

    public function render()
    {
        $tipeUjian = \App\Models\TipeUjian::all();
        return view('livewire.admin.sesi-ujian-create', compact('tipeUjian'));
    }
}
