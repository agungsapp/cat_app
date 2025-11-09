<?php

namespace App\Livewire\Admin\Master;

use Livewire\Component;
use App\Models\TipeUjian;
use App\Traits\HasAlert;
use Livewire\WithPagination;

class TipeUjianPage extends Component
{
    use WithPagination, HasAlert;

    protected string $paginationTheme = 'bootstrap';

    public $nama;
    public $max_attempt = '';          // tambah property
    public $search = '';
    public $tipeUjianId;
    public $updateMode = false;

    protected $rules = [
        'nama'        => 'required|string|max:255',
        'max_attempt' => 'nullable|integer|min:1', // boleh kosong (unlimited) atau angka >=1
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.master.tipe-ujian-page', [
            'listTipeUjian' => TipeUjian::where('nama', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function resetForm()
    {
        $this->reset(['nama', 'max_attempt', 'tipeUjianId', 'updateMode']);
        $this->max_attempt = ''; // pastikan kosong
    }

    public function store()
    {
        $this->validate();

        TipeUjian::create([
            'nama'        => $this->nama,
            'max_attempt' => $this->max_attempt ?: null,
        ]);

        $this->alertSuccess("Berhasil!", "Tipe ujian berhasil ditambahkan.");
        $this->resetForm();
    }

    public function edit($id)
    {
        $tipe = TipeUjian::findOrFail($id);

        $this->tipeUjianId = $tipe->id;
        $this->nama        = $tipe->nama;
        $this->max_attempt = $tipe->max_attempt; // isi field
        $this->updateMode  = true;
    }

    public function update()
    {
        $this->validate();

        $tipe = TipeUjian::findOrFail($this->tipeUjianId);

        $tipe->update([
            'nama'        => $this->nama,
            'max_attempt' => $this->max_attempt ?: null,
        ]);

        $this->alertSuccess("Berhasil!", "Tipe ujian berhasil diperbarui.");
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->alertConfirm(
            'Hapus Data?',
            'Yakin ingin menghapus tipe ujian ini?',
            'deleteConfirmed',
            ['id' => $id]
        );
    }

    public function deleteConfirmed($data)
    {
        $id = $data['id'] ?? null;
        if (!$id) return;

        TipeUjian::find($id)?->delete();

        $this->alertSuccess('Dihapus!', 'Tipe ujian berhasil dihapus.');
    }
}
