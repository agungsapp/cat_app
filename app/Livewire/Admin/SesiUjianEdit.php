<?php

namespace App\Livewire\Admin;

use App\Models\SesiUjian;
use App\Traits\HasAlert;
use Livewire\Component;

class SesiUjianEdit extends Component
{
    use HasAlert;

    public $sesiId;
    public $judul, $deskripsi, $tipe_ujian_id, $durasi_menit = 90;
    public $is_active = true;
    public $waktu_mulai, $waktu_selesai;

    protected $rules = [
        'judul' => 'required|string|max:255',
        'tipe_ujian_id' => 'required|exists:tipe_ujians,id',
        'durasi_menit' => 'required|integer|min:1',
        'waktu_mulai' => 'nullable|date',
        'waktu_selesai' => 'nullable|date|after:waktu_mulai',
    ];

    public function mount($id)
    {
        $this->sesiId = $id;
        $this->loadSesi();
    }

    public function loadSesi()
    {
        $sesi = SesiUjian::findOrFail($this->sesiId);

        $this->judul = $sesi->judul;
        $this->deskripsi = $sesi->deskripsi;
        $this->tipe_ujian_id = $sesi->tipe_ujian_id;
        $this->durasi_menit = $sesi->durasi_menit;
        $this->is_active = $sesi->is_active;
        $this->waktu_mulai = $sesi->waktu_mulai?->format('Y-m-d\TH:i');
        $this->waktu_selesai = $sesi->waktu_selesai?->format('Y-m-d\TH:i');
    }

    public function save()
    {
        $this->validate();

        $sesi = SesiUjian::findOrFail($this->sesiId);
        $sesi->update([
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'tipe_ujian_id' => $this->tipe_ujian_id,
            'durasi_menit' => $this->durasi_menit,
            'is_active' => $this->is_active,
            'waktu_mulai' => $this->waktu_mulai,
            'waktu_selesai' => $this->waktu_selesai,
        ]);

        $this->alertSuccess('Berhasil', 'Sesi ujian diperbarui!');
        return redirect()->route('admin.sesi-ujian.index');
    }

    public function render()
    {
        $tipeUjian = \App\Models\TipeUjian::all();
        return view('livewire.admin.sesi-ujian-edit', compact('tipeUjian'));
    }
}
