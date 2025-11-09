<?php

namespace App\Livewire\Admin\Materi;

use App\Models\Materi;
use App\Models\Topik;
use App\Traits\HasAlert;
use Livewire\Component;
use Livewire\WithPagination;

class MateriIndex extends Component
{
    use WithPagination, HasAlert;

    protected string $paginationTheme = 'bootstrap';

    public $topik;
    public $judul;
    public $search = '';
    public $materiId;
    public $updateMode = false;

    protected $rules = [
        'judul' => 'required|string|max:255',
    ];

    public function mount($topik)
    {
        $this->topik = Topik::findOrFail($topik);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $listMateri = Materi::where('topik_id', $this->topik->id)
            ->where('judul', 'like', '%' . $this->search . '%')
            ->orderBy('urutan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.materi.materi-index', [
            'listMateri' => $listMateri,
            'topik'      => $this->topik,
        ])->title('Kelola Materi - ' . $this->topik->nama_topik);
    }

    public function resetForm()
    {
        $this->reset(['judul', 'materiId', 'updateMode']);
    }

    public function store()
    {
        $this->validate();

        Materi::create([
            'topik_id' => $this->topik->id,
            'judul'    => $this->judul,
            'urutan'   => Materi::where('topik_id', $this->topik->id)->max('urutan') + 1,
        ]);

        $this->alertSuccess('Berhasil!', 'Materi berhasil ditambahkan.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $materi = Materi::findOrFail($id);
        $this->materiId   = $materi->id;
        $this->judul      = $materi->judul;
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate();

        $materi = Materi::findOrFail($this->materiId);
        $materi->update(['judul' => $this->judul]);

        $this->alertSuccess('Berhasil!', 'Materi berhasil diperbarui.');
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->alertConfirm(
            'Hapus Materi?',
            'Yakin ingin menghapus topik ini? Semua submateri & konten di dalamnya juga akan ikut terhapus.',
            'deleteConfirmed',
            ['id' => $id]
        );
    }

    public function deleteConfirmed($data)
    {
        $id = $data['id'] ?? null;
        if (!$id) return;

        Materi::find($id)?->delete();

        $this->alertSuccess('Dihapus!', 'Materi berhasil dihapus.');
    }
}
