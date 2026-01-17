<?php

namespace App\Livewire\Admin;

use App\Models\SesiUjian;
use App\Traits\HasAlert;
use Livewire\Component;

use App\Models\Soal;
use App\Models\SesiSoal;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;


class SesiUjianCreate extends Component
{
    use HasAlert;

    public $judul, $deskripsi, $tipe_ujian_id, $durasi_menit = 90;
    public $is_active = true;
    public $waktu_mulai, $waktu_selesai;
    public $mode = '';

    public $jumlah_soal;
    public $jenis_ujian_id;

    /**
     * Untuk fixed_rule
     * [
     *   ['jenis_ujian_id' => 1, 'jumlah_soal' => 10],
     *   ...
     * ]
     */
    public $komposisi = [];


    protected $rules = [
        'judul' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'tipe_ujian_id' => 'required|exists:tipe_ujians,id',
        'durasi_menit' => 'required|integer|min:1',
        'waktu_mulai' => 'nullable|date',
        'waktu_selesai' => 'nullable|date|after:waktu_mulai',
    ];

    protected $messages = [
        'judul.required' => 'Judul sesi wajib diisi.',
        'judul.max' => 'Judul maksimal 255 karakter.',

        'tipe_ujian_id.required' => 'Tipe ujian wajib dipilih.',
        'tipe_ujian_id.exists' => 'Tipe ujian tidak valid.',

        'durasi_menit.required' => 'Durasi ujian wajib diisi.',
        'durasi_menit.integer' => 'Durasi harus berupa angka.',
        'durasi_menit.min' => 'Durasi minimal 1 menit.',

        'waktu_mulai.date' => 'Format waktu mulai tidak valid.',
        'waktu_selesai.date' => 'Format waktu selesai tidak valid.',
        'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai.',
    ];


    private function validateMode()
    {
        if (!$this->mode) {
            throw ValidationException::withMessages([
                'tipe_ujian_id' => 'Mode ujian belum ditentukan'
            ]);
        }
    }

    private function validateModeSpecific()
    {
        match ($this->mode) {
            'random_all' => $this->validate([
                'jumlah_soal' => 'required|integer|min:1',
            ]),

            'random_by_jenis' => $this->validate([
                'jenis_ujian_id' => 'required|exists:jenis_ujians,id',
                'jumlah_soal' => 'required|integer|min:1',
            ]),

            'fixed_rule' => $this->validateFixedRule(), // ✅ Pindah ke method khusus

            default => throw ValidationException::withMessages([
                'tipe_ujian_id' => 'Mode ujian tidak valid'
            ])
        };
    }

    // ✅ TAMBAH METHOD BARU
    private function validateFixedRule()
    {
        $this->validate([
            'komposisi' => 'required|array|min:1',
            'komposisi.*.jenis_ujian_id' => 'required|exists:jenis_ujians,id',
            'komposisi.*.jumlah_soal' => 'required|integer|min:1',
        ]);

        // ✅ Validasi distinct jenis_ujian_id
        $jenisIds = collect($this->komposisi)->pluck('jenis_ujian_id')->filter();

        if ($jenisIds->count() !== $jenisIds->unique()->count()) {
            throw ValidationException::withMessages([
                'komposisi' => 'Jenis ujian tidak boleh duplikat dalam komposisi'
            ]);
        }
    }


    public function save()
    {
        $this->validate();
        $this->validateMode();
        $this->validateModeSpecific();

        DB::beginTransaction();

        try {
            $sesi = SesiUjian::create([
                'judul' => $this->judul,
                'deskripsi' => $this->deskripsi,
                'tipe_ujian_id' => $this->tipe_ujian_id,
                'durasi_menit' => $this->durasi_menit,
                'is_active' => $this->is_active,
                'waktu_mulai' => $this->waktu_mulai,
                'waktu_selesai' => $this->waktu_selesai,
            ]);

            match ($this->mode) {

                /** ---------------- RANDOM ALL ---------------- */
                'random_all' => $this->handleRandomAll($sesi),

                /** ------------- RANDOM BY JENIS ------------- */
                'random_by_jenis' => $this->handleRandomByJenis($sesi),

                /** ---------------- FIXED RULE ---------------- */
                'fixed_rule' => $this->handleFixedRule($sesi),

                default => throw ValidationException::withMessages([
                    'tipe_ujian_id' => 'Mode ujian tidak valid'
                ])
            };

            DB::commit();

            $this->alertSuccess('Berhasil', 'Sesi ujian berhasil dibuat & soal dikunci');
            return redirect()->route('admin.sesi-ujian.index');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->alertError('Error', $e->getMessage());
        }
    }


    private function attachSoalsToSesi(SesiUjian $sesi, $soalIds)
    {
        $data = collect($soalIds)->map(fn($id) => [
            'sesi_ujian_id' => $sesi->id,
            'soal_id' => $id,
        ])->toArray();

        SesiSoal::insert($data);
    }





    public function render()
    {
        $tipeUjian = \App\Models\TipeUjian::all();
        $jenisUjian = \App\Models\JenisUjian::all();
        return view('livewire.admin.sesi-ujian-create', compact(['tipeUjian', 'jenisUjian']));
    }

    public function updatedTipeUjianId($value)
    {
        $tipeUjian = \App\Models\TipeUjian::find($value);
        $this->mode = $tipeUjian?->getRawOriginal('mode');

        // reset input mode-specific
        $this->jumlah_soal = null;
        $this->jenis_ujian_id = null;
        $this->komposisi = [];

        // ✅ TAMBAH INI: Auto-add 1 row untuk fixed_rule
        if ($this->mode === 'fixed_rule') {
            $this->komposisi[] = [
                'jenis_ujian_id' => null,
                'jumlah_soal' => null,
            ];
        }
    }


    private function handleRandomAll(SesiUjian $sesi)
    {
        $soals = Soal::inRandomOrder()
            ->limit($this->jumlah_soal)
            ->pluck('id');

        if ($soals->count() < $this->jumlah_soal) {
            throw ValidationException::withMessages([
                'jumlah_soal' => 'Jumlah soal tidak mencukupi (tersedia: ' . $soals->count() . ')'
            ]);
        }

        $this->attachSoalsToSesi($sesi, $soals);
    }


    private function handleRandomByJenis(SesiUjian $sesi)
    {
        $soals = Soal::where('jenis_id', $this->jenis_ujian_id)
            ->inRandomOrder()
            ->limit($this->jumlah_soal)
            ->pluck('id');

        if ($soals->count() < $this->jumlah_soal) {
            $jenisNama = \App\Models\JenisUjian::find($this->jenis_ujian_id)->nama;
            throw ValidationException::withMessages([
                'jumlah_soal' => "Soal $jenisNama tidak mencukupi (tersedia: {$soals->count()})"
            ]);
        }

        $this->attachSoalsToSesi($sesi, $soals);
    }

    private function handleFixedRule(SesiUjian $sesi)
    {
        $usedSoalIds = [];

        foreach ($this->komposisi as $index => $rule) {
            $soals = Soal::where('jenis_id', $rule['jenis_ujian_id'])
                ->whereNotIn('id', $usedSoalIds)
                ->inRandomOrder()
                ->limit($rule['jumlah_soal'])
                ->pluck('id');

            if ($soals->count() < $rule['jumlah_soal']) {
                $jenisNama = \App\Models\JenisUjian::find($rule['jenis_ujian_id'])->nama;
                throw ValidationException::withMessages([
                    "komposisi.$index.jumlah_soal" => "Soal $jenisNama tidak mencukupi (tersedia: {$soals->count()})"
                ]);
            }

            $usedSoalIds = array_merge($usedSoalIds, $soals->toArray());
            $this->attachSoalsToSesi($sesi, $soals);
        }
    }

    public function addKomposisi()
    {
        $this->komposisi[] = [
            'jenis_ujian_id' => null,
            'jumlah_soal' => null,
        ];
    }

    public function removeKomposisi($index)
    {
        unset($this->komposisi[$index]);
        $this->komposisi = array_values($this->komposisi);
    }
}
