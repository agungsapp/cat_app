<?php

namespace App\Livewire\Admin\Master;

use Livewire\Component;
use App\Models\JenisUjian;
use App\Traits\HasAlert;
use Livewire\WithPagination;

class JenisUjianPage extends Component
{
    use WithPagination, HasAlert;

    protected string $paginationTheme = 'bootstrap';

    public $nama;
    public $search = '';
    public $jenisUjianId;
    public $updateMode = false;
    public $showModal = false; // Ganti dari showEditModal

    protected $rules = [
        'nama' => 'required|string|max:255',
    ];

    // Biar pagination reset saat search berubah
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.master.jenis-ujian-page', [
            'listJenisUjian' => JenisUjian::where('nama', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function resetForm()
    {
        $this->reset(['nama', 'jenisUjianId', 'updateMode', 'showModal']);
        $this->resetValidation();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        JenisUjian::create([
            'nama' => $this->nama,
        ]);

        $this->alertSuccess("Berhasil!", "Jenis ujian berhasil ditambahkan.");

        $this->resetForm();
    }

    public function edit($id)
    {
        $jenis = JenisUjian::findOrFail($id);
        $this->jenisUjianId = $jenis->id;
        $this->nama = $jenis->nama;
        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        $jenis = JenisUjian::findOrFail($this->jenisUjianId);
        $jenis->update(['nama' => $this->nama]);

        $this->alertSuccess("Berhasil!", "Jenis ujian berhasil diperbarui.");

        $this->resetForm();
    }

    public function closeModal()
    {
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->alertConfirm(
            'Hapus Data?',
            'Yakin ingin menghapus jenis ujian ini?',
            'deleteConfirmed',
            ['id' => $id]
        );
    }

    public function deleteConfirmed($data)
    {
        $id = $data['id'] ?? null;
        if (! $id) return;

        JenisUjian::find($id)?->delete();

        $this->alertSuccess('Dihapus!', 'Jenis ujian berhasil dihapus.');
    }
}
