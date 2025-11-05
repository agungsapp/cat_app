<?php

namespace App\Livewire\Admin;

use App\Models\Soal;
use App\Models\SoalOpsi;
use App\Models\JenisUjian;
use App\Traits\HasAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class BankSoalPage extends Component
{
    use HasAlert, WithFileUploads, WithPagination;

    // Filter & Search
    public $search = '';
    public $filterJenis = '';

    // Form Properties
    public $soalId;
    public $jenis_id;
    public $pertanyaan_text;
    public $media_type = 'none';
    public $media_file;
    public $skor = 1;
    public array $opsi = [];
    public $correctAnswerIndex = null;

    // Modal State
    public $showModal = false;
    public $isEdit = false;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->resetOpsi();
    }

    public function resetOpsi()
    {
        $this->opsi = [
            ['label' => 'A', 'teks' => '', 'media_type' => 'none', 'media_file' => null, 'is_correct' => false],
            ['label' => 'B', 'teks' => '', 'media_type' => 'none', 'media_file' => null, 'is_correct' => false],
            ['label' => 'C', 'teks' => '', 'media_type' => 'none', 'media_file' => null, 'is_correct' => false],
            ['label' => 'D', 'teks' => '', 'media_type' => 'none', 'media_file' => null, 'is_correct' => false],
        ];
        $this->correctAnswerIndex = null;
    }

    public function addOpsi()
    {
        $labels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $nextLabel = $labels[count($this->opsi)] ?? '';

        if ($nextLabel) {
            $this->opsi[] = [
                'label' => $nextLabel,
                'teks' => '',
                'media_type' => 'none',
                'media_file' => null,
                'is_correct' => false
            ];
        }
    }

    public function removeOpsi($index)
    {
        if (count($this->opsi) > 2) {
            unset($this->opsi[$index]);
            $this->opsi = array_values($this->opsi);
        }
    }

    public function setCorrect($index)
    {
        $this->correctAnswerIndex = $index;
        foreach ($this->opsi as $key => $value) {
            $this->opsi[$key]['is_correct'] = ($key == $index);
        }
    }

    public function updatedCorrectAnswerIndex($value)
    {
        foreach ($this->opsi as $key => $item) {
            $this->opsi[$key]['is_correct'] = ($key == $value);
        }
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->isEdit = false;
    }

    public function edit($id)
    {
        $soal = Soal::with('opsi')->findOrFail($id);

        $this->soalId = $soal->id;
        $this->jenis_id = $soal->jenis_id;
        $this->pertanyaan_text = $soal->pertanyaan_text;
        $this->media_type = $soal->media_type;
        $this->skor = $soal->skor;

        // Reset correctAnswerIndex dulu
        $this->correctAnswerIndex = null;

        // Konversi collection ke array dengan benar
        $this->opsi = [];
        foreach ($soal->opsi as $index => $item) {
            if ($item->is_correct) {
                $this->correctAnswerIndex = $index;
            }
            $this->opsi[] = [
                'id' => $item->id,
                'label' => $item->label,
                'teks' => $item->teks,
                'media_type' => $item->media_type,
                'media_path' => $item->media_path,
                'media_file' => null,
                'is_correct' => $item->is_correct
            ];
        }

        $this->showModal = true;
        $this->isEdit = true;
    }

    public function save()
    {
        $this->validate([
            'jenis_id' => 'required|exists:jenis_ujians,id',
            'pertanyaan_text' => 'nullable|string',
            'media_type' => 'required|in:none,image,audio',
            'media_file' => $this->media_type !== 'none' ? 'nullable|file|max:10240' : '',
            'skor' => 'required|integer|min:1',
            'opsi' => 'required|array|min:2',
        ]);

        // Validasi: minimal ada 1 jawaban benar
        $hasCorrect = collect($this->opsi)->contains('is_correct', true);
        if (!$hasCorrect) {
            $this->alertError('Error', 'Pilih minimal 1 jawaban yang benar!');
            return;
        }

        try {
            // Upload media pertanyaan
            $mediaPath = null;
            if ($this->media_file) {
                $mediaPath = $this->media_file->store('soal', 'public');
            }

            // Save atau Update Soal
            $soal = Soal::updateOrCreate(
                ['id' => $this->soalId],
                [
                    'jenis_id' => $this->jenis_id,
                    'pertanyaan_text' => $this->pertanyaan_text,
                    'media_type' => $this->media_type,
                    'media_path' => $mediaPath ?? ($this->isEdit ? Soal::find($this->soalId)->media_path : null),
                    'skor' => $this->skor,
                ]
            );

            // Hapus opsi lama jika edit
            if ($this->isEdit) {
                SoalOpsi::where('soal_id', $soal->id)->delete();
            }

            // Save Opsi
            foreach ($this->opsi as $item) {
                $opsiMediaPath = null;
                if (isset($item['media_file']) && $item['media_file']) {
                    $opsiMediaPath = $item['media_file']->store('opsi', 'public');
                }

                SoalOpsi::create([
                    'soal_id' => $soal->id,
                    'label' => $item['label'],
                    'teks' => $item['teks'],
                    'media_type' => $item['media_type'],
                    'media_path' => $opsiMediaPath,
                    'is_correct' => $item['is_correct'],
                ]);
            }

            $this->alertSuccess('Berhasil', $this->isEdit ? 'Soal berhasil diupdate!' : 'Soal berhasil ditambahkan!');
            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->alertError('Error', $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->alertConfirm(
            'Hapus Soal?',
            'Data soal dan semua opsi akan dihapus permanent!',
            'delete',
            ['id' => $id]
        );
    }

    public function delete($id)
    {
        try {
            $soal = Soal::findOrFail($id);

            // Hapus file media
            if ($soal->media_path) {
                Storage::disk('public')->delete($soal->media_path);
            }

            // Hapus file media opsi
            foreach ($soal->opsi as $opsi) {
                if ($opsi->media_path) {
                    Storage::disk('public')->delete($opsi->media_path);
                }
            }

            $soal->delete();

            $this->alertSuccess('Berhasil', 'Soal berhasil dihapus!');
            $this->resetPage();
        } catch (\Exception $e) {
            $this->alertError('Error', $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function resetForm()
    {
        $this->soalId = null;
        $this->jenis_id = null;
        $this->pertanyaan_text = null;
        $this->media_type = 'none';
        $this->media_file = null;
        $this->skor = 1;
        $this->correctAnswerIndex = null;
        $this->resetOpsi();
        $this->isEdit = false;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterJenis()
    {
        $this->resetPage();
    }

    public function render()
    {

        // dd($this->opsi);
        $jenisUjian = JenisUjian::all();

        $soals = Soal::with(['jenis', 'opsi'])
            ->when($this->search, function ($q) {
                $q->where('pertanyaan_text', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterJenis, function ($q) {
                $q->where('jenis_id', $this->filterJenis);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.bank-soal-page', [
            'soals' => $soals,
            'jenisUjian' => $jenisUjian,
        ]);
    }
}
