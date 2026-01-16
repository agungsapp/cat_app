<?php

namespace App\Livewire\Admin;

use App\Models\Soal;
use App\Models\SoalOpsi;
use App\Models\JenisUjian;
use App\Traits\HasAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class BankSoalEdit extends Component
{
    use HasAlert, WithFileUploads;

    public $soalId;
    public $jenis_id;
    public $pertanyaan_text;
    public $media_type = 'none';
    public $media_file;
    // public $skor = 1;
    public $tipe_penilaian;
    public $opsi = [];
    public $correctAnswerIndex = null;

    public $oldMediaPath; // Untuk hapus file lama

    protected $rules = [
        'jenis_id' => 'required|exists:jenis_ujians,id',
        'pertanyaan_text' => 'nullable|string',
        'media_type' => 'required|in:none,image,audio',
        'media_file' => 'nullable|file|max:10240',
        // 'skor' => 'required|integer|min:1',
        'opsi' => 'required|array|min:2',
        'opsi.*.media_type' => 'required|in:none,image,audio',
    ];

    public function mount($id)
    {
        $this->soalId = $id;
        $this->loadSoal();
    }

    public function loadSoal()
    {
        $soal = Soal::with('opsi', 'jenis')->findOrFail($this->soalId);

        $this->jenis_id = $soal->jenis_id;
        $this->pertanyaan_text = $soal->pertanyaan_text;
        $this->media_type = $soal->media_type;
        $this->oldMediaPath = $soal->media_path;
        $this->tipe_penilaian = $soal->jenis->tipe_penilaian;
        // $this->skor = $soal->skor;

        $this->opsi = [];
        $this->correctAnswerIndex = null;

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
                'is_correct' => $item->is_correct,
                'skor' => $item->skor, // 🔥 WAJIB
            ];
        }
    }

    public function addOpsi()
    {
        if (count($this->opsi) >= 8) return;

        $labels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $nextLabel = $labels[count($this->opsi)] ?? null;
        if (!$nextLabel) return;

        $this->opsi[] = [
            'label' => $nextLabel,
            'teks' => '',
            'media_type' => 'none',
            'media_file' => null,
            'is_correct' => false,
            'skor' => null,
        ];
    }

    public function removeOpsi($index)
    {
        if (count($this->opsi) <= 2) return;

        // Hapus file lama jika ada
        if (isset($this->opsi[$index]['media_path']) && $this->opsi[$index]['media_path']) {
            Storage::disk('public')->delete($this->opsi[$index]['media_path']);
        }

        unset($this->opsi[$index]);
        $this->opsi = array_values($this->opsi);

        if ($this->correctAnswerIndex == $index) {
            $this->correctAnswerIndex = null;
        } elseif ($this->correctAnswerIndex > $index) {
            $this->correctAnswerIndex--;
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

    public function updatedJenisId($value)
    {
        $jenis = JenisUjian::find($value);
        $this->tipe_penilaian = $jenis?->tipe_penilaian;

        // reset logic sesuai tipe
        foreach ($this->opsi as $i => $opsi) {
            $this->opsi[$i]['is_correct'] = false;
            $this->opsi[$i]['skor'] = null;
        }

        $this->correctAnswerIndex = null;
    }


    public function save()
    {
        $this->validate();

        // Validasi manual opsi
        foreach ($this->opsi as $index => $item) {
            if ($item['media_type'] === 'none' && empty(trim($item['teks'] ?? ''))) {
                $this->addError("opsi.{$index}.teks", 'Teks opsi wajib diisi.');
                return;
            }
            if (in_array($item['media_type'], ['image', 'audio']) && empty($item['media_file']) && empty($item['media_path'])) {
                $this->addError("opsi.{$index}.media_file", 'File wajib diunggah.');
                return;
            }
        }

        $jenis = JenisUjian::findOrFail($this->jenis_id);

        if ($jenis->tipe_penilaian === 'benar_salah') {
            if (!collect($this->opsi)->contains('is_correct', true)) {
                $this->addError('correctAnswerIndex', 'Pilih satu jawaban benar!');
                return;
            }
        }

        if ($jenis->tipe_penilaian === 'bobot_opsi') {
            foreach ($this->opsi as $i => $opsi) {
                if (!isset($opsi['skor']) || $opsi['skor'] < 1 || $opsi['skor'] > 5) {
                    $this->addError("opsi.$i.skor", 'Skor wajib 1–5.');
                    return;
                }
            }
        }


        try {
            $soal = Soal::findOrFail($this->soalId);

            // Upload media baru (jika ada)
            $mediaPath = $soal->media_path;
            if ($this->media_file && $this->media_type !== 'none') {
                // Hapus lama
                if ($soal->media_path && Storage::disk('public')->exists($soal->media_path)) {
                    Storage::disk('public')->delete($soal->media_path);
                }
                $mediaPath = $this->media_file->store('soal', 'public');
            } elseif ($this->media_type === 'none') {
                // Hapus media jika diubah ke teks
                if ($soal->media_path && Storage::disk('public')->exists($soal->media_path)) {
                    Storage::disk('public')->delete($soal->media_path);
                }
                $mediaPath = null;
            }

            // Update soal
            $soal->update([
                'jenis_id' => $this->jenis_id,
                'pertanyaan_text' => $this->pertanyaan_text,
                'media_type' => $this->media_type,
                'media_path' => $mediaPath,
                // 'skor' => $this->skor,
            ]);

            // Hapus semua opsi lama
            foreach ($soal->opsi as $oldOpsi) {
                if ($oldOpsi->media_path && Storage::disk('public')->exists($oldOpsi->media_path)) {
                    Storage::disk('public')->delete($oldOpsi->media_path);
                }
                $oldOpsi->delete();
            }

            // Simpan opsi baru
            foreach ($this->opsi as $item) {
                $opsiMediaPath = $item['media_path'] ?? null;
                if (isset($item['media_file']) && $item['media_file'] && $item['media_type'] !== 'none') {
                    $opsiMediaPath = $item['media_file']->store('opsi', 'public');
                }

                SoalOpsi::create([
                    'soal_id' => $soal->id,
                    'label' => $item['label'],
                    'teks' => $item['teks'] ?? '',
                    'media_type' => $item['media_type'],
                    'media_path' => $opsiMediaPath,
                    'is_correct' => $jenis->tipe_penilaian === 'benar_salah'
                        ? $item['is_correct']
                        : false,
                    'skor' => $jenis->tipe_penilaian === 'bobot_opsi'
                        ? $item['skor']
                        : null,
                ]);
            }

            $this->alertSuccess('Berhasil!', 'Soal berhasil diperbarui!');
            return redirect()->route('admin.bank-soal.index');
        } catch (\Exception $e) {
            $this->alertError('Error', $e->getMessage());
        }
    }

    public function render()
    {
        $jenisUjian = JenisUjian::all();

        return view('livewire.admin.bank-soal-edit', [
            'jenisUjian' => $jenisUjian,
        ]);
    }
}
