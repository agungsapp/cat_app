<?php

namespace App\Livewire\Admin\Materi;

use App\Models\Topik;
use App\Traits\HasAlert;
use Livewire\Component;
use Livewire\WithPagination;

class TopikIndex extends Component
{
    use WithPagination, HasAlert;

    protected string $paginationTheme = 'bootstrap';

    public $nama_topik;
    public $search = '';
    public $topikId;
    public $updateMode = false;

    protected $rules = [
        'nama_topik' => 'required|string|max:255',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $listTopik = Topik::where('nama_topik', 'like', '%' . $this->search . '%')
            ->orderBy('urutan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.materi.topik-index', [
            'listTopik' => $listTopik,
        ])->title('Kelola Topik');
    }

    public function resetForm()
    {
        $this->reset(['nama_topik', 'topikId', 'updateMode']);
    }

    public function store()
    {
        $this->validate();

        Topik::create([
            'nama_topik' => $this->nama_topik,
            'urutan'     => Topik::max('urutan') + 1,
        ]);

        $this->alertSuccess('Berhasil!', 'Topik berhasil ditambahkan.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $topik = Topik::findOrFail($id);
        $this->topikId     = $topik->id;
        $this->nama_topik  = $topik->nama_topik;
        $this->updateMode  = true;
    }

    public function update()
    {
        $this->validate();

        $topik = Topik::findOrFail($this->topikId);
        $topik->update(['nama_topik' => $this->nama_topik]);

        $this->alertSuccess('Berhasil!', 'Topik berhasil diperbarui.');
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->alertConfirm(
            'Hapus Topik?',
            'Yakin ingin menghapus topik ini? Semua materi, submateri, dan konten di dalamnya akan ikut terhapus.',
            'deleteConfirmed',
            ['id' => $id]
        );
    }

    public function deleteConfirmed($data)
    {
        $id = $data['id'] ?? null;
        if (!$id) return;

        Topik::find($id)?->delete();

        $this->alertSuccess('Dihapus!', 'Topik berhasil dihapus.');
    }
}
