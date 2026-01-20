<?php

namespace App\Livewire\Admin\Materi;

use App\Models\Konten;
use App\Models\Materi;
use App\Models\Submateri;
use App\Models\Topik;
use App\Traits\HasAlert;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class KontenIndex extends Component
{
    use WithPagination, HasAlert, WithFileUploads;

    protected string $paginationTheme = 'bootstrap';

    public $topik;
    public $materi;
    public $submateri;

    // Form fields
    public $tipe = 'video';
    public $youtube_url;
    public $isi;
    public $file;
    public $search = '';
    public $kontenId;
    public $updateMode = false;
    public $showModal = false;

    // VALIDASI DINAMIS
    protected function rules()
    {
        $rules = [
            'tipe' => 'required|in:video,pdf',
            'isi'  => 'nullable|string',
        ];

        if ($this->tipe === 'video') {
            $rules['youtube_url'] = 'required|url';
        } elseif ($this->tipe === 'pdf') {
            $rules['file'] = 'required|file|mimes:pdf|max:51200';
        }

        return $rules;
    }

    protected $messages = [
        'youtube_url.required' => 'Link YouTube wajib diisi untuk tipe Video.',
        'youtube_url.url'      => 'Link YouTube harus valid[](https://...)',
        'file.required'        => 'File PDF wajib diupload untuk tipe PDF.',
        'file.mimes'           => 'File harus berupa PDF.',
        'file.max'             => 'Ukuran PDF maksimal 50MB.',
    ];

    public function mount($topik, $materi, $submateri)
    {
        $this->topik      = Topik::findOrFail($topik);
        $this->materi     = Materi::findOrFail($materi);
        $this->submateri  = Submateri::findOrFail($submateri);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatingTipe($value)
    {
        $this->reset(['youtube_url', 'file', 'isi']);
        $this->resetValidation();
    }

    public function render()
    {
        $listKonten = Konten::where('submateri_id', $this->submateri->id)
            ->where(function ($q) {
                $q->where('tipe', 'like', '%' . $this->search . '%')
                    ->orWhere('isi', 'like', '%' . $this->search . '%')
                    ->orWhere('file_path', 'like', '%' . $this->search . '%');
            })
            ->orderBy('urutan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.materi.konten-index', [
            'listKonten' => $listKonten,
            'topik'      => $this->topik,
            'materi'     => $this->materi,
            'submateri'  => $this->submateri,
        ])->title('Kelola Konten - ' . $this->submateri->judul);
    }

    public function resetForm()
    {
        $this->reset(['tipe', 'youtube_url', 'isi', 'file', 'kontenId', 'updateMode', 'showModal']);
        $this->resetValidation();
        $this->tipe = 'video'; // default kembali ke video
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        $data = [
            'submateri_id' => $this->submateri->id,
            'tipe'         => $this->tipe,
            'isi'          => $this->isi,
            'file_path'    => null,
            'urutan'       => Konten::where('submateri_id', $this->submateri->id)->max('urutan') + 1,
        ];

        if ($this->tipe === 'video') {
            $data['file_path'] = $this->youtube_url;
        } elseif ($this->tipe === 'pdf' && $this->file) {
            $data['file_path'] = $this->file->store('konten/pdf', 'public');
        }

        Konten::create($data);

        $this->alertSuccess('Berhasil!', 'Konten berhasil ditambahkan.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $konten = Konten::findOrFail($id);
        $this->kontenId    = $konten->id;
        $this->tipe        = $konten->tipe;
        $this->isi         = $konten->isi;

        if ($konten->tipe === 'video') {
            $this->youtube_url = $konten->file_path;
            $this->file        = null;
        } else {
            $this->youtube_url = null;
        }

        $this->updateMode = true;
        $this->showModal  = true;
    }

    public function update()
    {
        $this->validate();

        $konten = Konten::findOrFail($this->kontenId);

        $data = [
            'tipe' => $this->tipe,
            'isi'  => $this->isi,
        ];

        if ($this->tipe === 'video') {
            $data['file_path'] = $this->youtube_url;

            // Jika sebelumnya PDF, hapus file lama
            if ($konten->tipe === 'pdf' && $konten->file_path) {
                Storage::disk('public')->delete($konten->file_path);
            }
        } elseif ($this->tipe === 'pdf') {
            if ($this->file) {
                if ($konten->file_path) {
                    Storage::disk('public')->delete($konten->file_path);
                }
                $data['file_path'] = $this->file->store('konten/pdf', 'public');
            }
            // Jika sebelumnya video, tidak perlu hapus apa-apa (hanya string URL)
        }

        $konten->update($data);

        $this->alertSuccess('Berhasil!', 'Konten berhasil diperbarui.');
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->alertConfirm(
            'Hapus Konten?',
            'Yakin ingin menghapus konten ini?',
            'deleteConfirmed',
            ['id' => $id]
        );
    }

    public function deleteConfirmed($data)
    {
        $id = $data['id'] ?? null;
        if (!$id) return;

        $konten = Konten::find($id);
        if ($konten) {
            if ($konten->tipe === 'pdf' && $konten->file_path) {
                Storage::disk('public')->delete($konten->file_path);
            }
            $konten->delete();
        }

        $this->alertSuccess('Dihapus!', 'Konten berhasil dihapus.');
    }
}
