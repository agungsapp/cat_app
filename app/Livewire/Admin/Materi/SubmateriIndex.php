<?php

namespace App\Livewire\Admin\Materi;

use App\Models\Materi;
use App\Models\Submateri;
use App\Models\Topik;
use App\Traits\HasAlert;
use Livewire\Component;
use Livewire\WithPagination;

class SubmateriIndex extends Component
{
    use WithPagination, HasAlert;

    protected string $paginationTheme = 'bootstrap';

    public $topik;
    public $materi;
    public $judul;
    public $search = '';
    public $submateriId;
    public $updateMode = false;
    public $showModal = false;

    protected $rules = [
        'judul' => 'required|string|max:255',
    ];

    public function mount($topik, $materi)
    {
        $this->topik  = Topik::findOrFail($topik);
        $this->materi = Materi::findOrFail($materi);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $listSubmateri = Submateri::where('materi_id', $this->materi->id)
            ->where('judul', 'like', '%' . $this->search . '%')
            ->orderBy('urutan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.materi.submateri-index', [
            'listSubmateri' => $listSubmateri,
            'topik'         => $this->topik,
            'materi'        => $this->materi,
        ])->title('Kelola Submateri - ' . $this->materi->judul);
    }

    public function resetForm()
    {
        $this->reset(['judul', 'submateriId', 'updateMode', 'showModal']);
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

        Submateri::create([
            'materi_id' => $this->materi->id,
            'judul'     => $this->judul,
            'urutan'    => Submateri::where('materi_id', $this->materi->id)->max('urutan') + 1,
        ]);

        $this->alertSuccess('Berhasil!', 'Submateri berhasil ditambahkan.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $sub = Submateri::findOrFail($id);
        $this->submateriId = $sub->id;
        $this->judul       = $sub->judul;
        $this->updateMode  = true;
        $this->showModal   = true;
    }

    public function update()
    {
        $this->validate();

        $sub = Submateri::findOrFail($this->submateriId);
        $sub->update(['judul' => $this->judul]);

        $this->alertSuccess('Berhasil!', 'Submateri berhasil diperbarui.');
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->alertConfirm(
            'Hapus Submateri?',
            'Yakin ingin menghapus submateri ini? Semua konten di dalamnya akan ikut terhapus.',
            'deleteConfirmed',
            ['id' => $id]
        );
    }

    public function deleteConfirmed($data)
    {
        $id = $data['id'] ?? null;
        if (!$id) return;

        Submateri::find($id)?->delete();

        $this->alertSuccess('Dihapus!', 'Submateri berhasil dihapus.');
    }
}
