<?php

namespace App\Livewire\Admin;

use App\Models\Soal;
use App\Models\SoalOpsi;
use App\Models\JenisUjian;
use App\Traits\HasAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class BankSoalCreate extends Component
{
    use HasAlert, WithFileUploads;

    // Form
    public $jenis_id;
    public $pertanyaan_text;
    public $media_type = 'none';
    public $media_file;
    // public $skor = 1;
    public $tipe_penilaian; // cache dari jenis ujian
    public $opsi = [];
    public $correctAnswerIndex = null;

    protected $rules = [
        'jenis_id' => 'required|exists:jenis_ujians,id',
        'pertanyaan_text' => 'nullable|string',
        'media_type' => 'required|in:none,image,audio',
        'media_file' => 'nullable|file|max:10240', // 10MB
        // 'skor' => 'required|integer|min:1',
        'opsi' => 'required|array|min:2',
        // 'opsi.*.teks' => 'required_if:opsi.*.media_type,none|string',
        // 'opsi.*.media_file' => 'required_if:opsi.*.media_type,image,audio|file|max:10240',
        'opsi.*.media_type' => 'required|in:none,image,audio',
    ];

    public function mount()
    {
        $this->resetOpsi();
    }

    public function resetOpsi()
    {
        $this->opsi = [
            ['label' => 'A', 'teks' => '', 'media_type' => 'none', 'media_file' => null, 'is_correct' => false, 'skor' => null],
            ['label' => 'B', 'teks' => '', 'media_type' => 'none', 'media_file' => null, 'is_correct' => false, 'skor' => null],
            ['label' => 'C', 'teks' => '', 'media_type' => 'none', 'media_file' => null, 'is_correct' => false, 'skor' => null],
            ['label' => 'D', 'teks' => '', 'media_type' => 'none', 'media_file' => null, 'is_correct' => false, 'skor' => null],
        ];
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

        // === VALIDASI MANUAL OPSI ===
        foreach ($this->opsi as $index => $item) {
            if ($item['media_type'] === 'none' && empty(trim($item['teks'] ?? ''))) {
                $this->addError("opsi.{$index}.teks", 'Teks opsi wajib diisi jika tipe teks.');
                return;
            }
            if (in_array($item['media_type'], ['image', 'audio']) && empty($item['media_file'])) {
                $this->addError("opsi.{$index}.media_file", 'File wajib diunggah untuk tipe ' . ucfirst($item['media_type']) . '.');
                return;
            }
        }

        $jenis = JenisUjian::findOrFail($this->jenis_id);

        if ($jenis->tipe_penilaian === 'benar_salah') {
            if (!collect($this->opsi)->contains('is_correct', true)) {
                $this->addError('correctAnswerIndex', 'Pilih satu jawaban benar.');
                return;
            }
        }

        if ($jenis->tipe_penilaian === 'bobot_opsi') {
            foreach ($this->opsi as $i => $opsi) {
                if (!isset($opsi['skor']) || $opsi['skor'] < 1 || $opsi['skor'] > 5) {
                    $this->addError("opsi.$i.skor", 'Skor wajib 1–5 untuk TKP.');
                    return;
                }
            }
        }


        try {
            // Upload media soal
            $mediaPath = null;
            if ($this->media_file && $this->media_type !== 'none') {
                $mediaPath = $this->media_file->store('soal', 'public');
            }

            // Simpan soal
            $soal = Soal::create([
                'jenis_id' => $this->jenis_id,
                'pertanyaan_text' => $this->pertanyaan_text,
                'media_type' => $this->media_type,
                'media_path' => $mediaPath,
                // 'skor' => $this->skor,
            ]);

            // Simpan opsi
            foreach ($this->opsi as $item) {
                $opsiMediaPath = null;
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

            $this->alertSuccess('Berhasil!', 'Soal berhasil ditambahkan!');
            return redirect()->route('admin.bank-soal.index');
        } catch (\Exception $e) {
            $this->alertError('Error', $e->getMessage());
        }
        $this->alertError('Error', "Terjadil kesalahan saat menyimpan soal.");
    }

    public function render()
    {
        $jenisUjian = JenisUjian::all();

        return view('livewire.admin.bank-soal-create', [
            'jenisUjian' => $jenisUjian,
        ]);
    }
}
