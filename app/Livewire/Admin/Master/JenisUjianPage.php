<?php

namespace App\Livewire\Admin\Master;

use Livewire\Component;
use App\Models\JenisUjian;
// use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class JenisUjianPage extends Component
{

    public $nama;
    public $jenisUjianId;
    public $updateMode = false;

    protected $rules = [
        'nama' => 'required|string|max:255',
    ];

    public function render()
    {
        return view('livewire.admin.master.jenis-ujian-page', [
            'listJenisUjian' => JenisUjian::latest()->get(),
        ]);
    }

    public function resetForm()
    {
        $this->reset(['nama', 'jenisUjianId', 'updateMode']);
    }

    public function store()
    {
        $this->validate();

        JenisUjian::create([
            'nama' => $this->nama,
        ]);

        LivewireAlert::success('success', 'Berhasil!', [
            'text' => 'Jenis ujian berhasil ditambahkan.',
        ]);

        $this->resetForm();
    }

    public function edit($id)
    {
        $jenis = JenisUjian::findOrFail($id);
        $this->jenisUjianId = $jenis->id;
        $this->nama = $jenis->nama;
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate();

        $jenis = JenisUjian::findOrFail($this->jenisUjianId);
        $jenis->update(['nama' => $this->nama]);

        LivewireAlert::success('success', 'Berhasil!', [
            'text' => 'Jenis ujian berhasil diperbarui.',
        ]);

        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        LivewireAlert::confirm('Apakah kamu yakin ingin menghapus data ini?', [
            'onConfirmed' => "deleteConfirmed($id)",
            'cancelButtonText' => 'Batal',
            'confirmButtonText' => 'Ya, Hapus',
        ]);
    }

    public function deleteConfirmed($id)
    {
        JenisUjian::find($id)?->delete();

        LivewireAlert::success('success', 'Dihapus!', [
            'text' => 'Jenis ujian berhasil dihapus.',
        ]);
    }
}
