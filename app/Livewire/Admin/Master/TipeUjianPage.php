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
    public $search = '';
    public $tipeUjianId;
    public $updateMode = false;

    protected $rules = [
        'nama' => 'required|string|max:255',
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
        $this->reset(['nama', 'tipeUjianId', 'updateMode']);
    }

    public function store()
    {
        $this->validate();

        TipeUjian::create([
            'nama' => $this->nama,
        ]);

        $this->alertSuccess("Berhasil!", "Tipe ujian berhasil ditambahkan.");

        $this->resetForm();
    }

    public function edit($id)
    {
        $tipe = TipeUjian::findOrFail($id);
        $this->tipeUjianId = $tipe->id;
        $this->nama = $tipe->nama;
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate();

        $tipe = TipeUjian::findOrFail($this->tipeUjianId);
        $tipe->update(['nama' => $this->nama]);

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
        if (! $id) return;

        TipeUjian::find($id)?->delete();

        $this->alertSuccess('Dihapus!', 'Tipe ujian berhasil dihapus.');
    }
}
