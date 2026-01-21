<?php

namespace App\Livewire\Admin;

use App\Models\SesiUjian;
use App\Models\Soal;
use App\Models\SesiSoal;
use App\Traits\HasAlert;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class SesiUjianEdit extends Component
{
    use HasAlert;

    public $sesiId;
    public $judul, $deskripsi, $tipe_ujian_id, $durasi_menit = 90;
    public $is_active = true;
    public $waktu_mulai, $waktu_selesai;
    public $mode = '';

    // Mode-specific properties
    public $jumlah_soal;
    public $jenis_ujian_id;
    public $komposisi = [];

    protected $rules = [
        'judul' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'tipe_ujian_id' => 'required|exists:tipe_ujians,id',
        'durasi_menit' => 'required|integer|min:1',
        'waktu_mulai' => 'nullable|date',
        'waktu_selesai' => 'nullable|date|after:waktu_mulai',
    ];

    public function mount($id)
    {
        $this->sesiId = $id;
        $this->loadSesi();
        $this->checkEditable();
    }

    public function checkEditable()
    {
        $sesi = SesiUjian::findOrFail($this->sesiId);

        if ($sesi->hasilUjian()->exists()) {
            $this->alertError(
                'Peringatan Tidak Bisa Diedit',
                'Sesi ujian ini sudah dikerjakan peserta. Pengeditan tidak diperbolehkan.'
            );

            $this->dispatch('redirect-after-delay');
        }
    }


    public function loadSesi()
    {
        $sesi = SesiUjian::with('tipeUjian')->findOrFail($this->sesiId);

        // Basic info
        $this->judul = $sesi->judul;
        $this->deskripsi = $sesi->deskripsi;
        $this->tipe_ujian_id = $sesi->tipe_ujian_id;
        $this->durasi_menit = $sesi->durasi_menit;
        $this->is_active = $sesi->is_active;
        $this->waktu_mulai = $sesi->waktu_mulai?->format('Y-m-d\TH:i');
        $this->waktu_selesai = $sesi->waktu_selesai?->format('Y-m-d\TH:i');

        // Load mode
        $this->mode = $sesi->tipeUjian->getRawOriginal('mode');

        // Load mode-specific data
        $this->loadModeSpecificData($sesi);
    }

    private function loadModeSpecificData(SesiUjian $sesi)
    {
        match ($this->mode) {
            'random_all' => $this->loadRandomAllData($sesi),
            'random_by_jenis' => $this->loadRandomByJenisData($sesi),
            'fixed_rule' => $this->loadFixedRuleData($sesi),
            default => null
        };
    }

    private function loadRandomAllData(SesiUjian $sesi)
    {
        // Jumlah soal = total soal yang terkunci di sesi ini
        $this->jumlah_soal = $sesi->soal()->count();
    }

    private function loadRandomByJenisData(SesiUjian $sesi)
    {
        // Ambil soal pertama untuk tahu jenis apa yang dipakai
        $firstSoal = $sesi->soal()->with('jenis')->first();

        if ($firstSoal) {
            $this->jenis_ujian_id = $firstSoal->jenis_id;
            $this->jumlah_soal = $sesi->soal()->count();
        }
    }

    private function loadFixedRuleData(SesiUjian $sesi)
    {
        // dd($sesi->soal());

        // Group soal per jenis
        $soalsByJenis = $sesi->soal()
            ->with('jenis')
            ->get()
            ->groupBy('jenis_id');

        $this->komposisi = $soalsByJenis->map(function ($soals, $jenisId) {
            return [
                'jenis_ujian_id' => $jenisId,
                'jumlah_soal' => $soals->count(),
            ];
        })->values()->toArray();

        // Jika kosong, auto-add 1 row
        if (empty($this->komposisi)) {
            $this->komposisi[] = [
                'jenis_ujian_id' => null,
                'jumlah_soal' => null,
            ];
        }
    }

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

            'fixed_rule' => $this->validateFixedRule(),

            default => throw ValidationException::withMessages([
                'tipe_ujian_id' => 'Mode ujian tidak valid'
            ])
        };
    }

    private function validateFixedRule()
    {
        $this->validate([
            'komposisi' => 'required|array|min:1',
            'komposisi.*.jenis_ujian_id' => 'required|exists:jenis_ujians,id',
            'komposisi.*.jumlah_soal' => 'required|integer|min:1',
        ]);

        // Validasi distinct jenis_ujian_id
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
            $sesi = SesiUjian::findOrFail($this->sesiId);

            // Update basic info
            $sesi->update([
                'judul' => $this->judul,
                'deskripsi' => $this->deskripsi,
                'tipe_ujian_id' => $this->tipe_ujian_id,
                'durasi_menit' => $this->durasi_menit,
                'is_active' => $this->is_active,
                'waktu_mulai' => $this->waktu_mulai,
                'waktu_selesai' => $this->waktu_selesai,
            ]);

            // ✅ DROP semua soal lama
            $sesi->soal()->detach();

            // ✅ Regenerate soal baru sesuai mode
            match ($this->mode) {
                'random_all' => $this->handleRandomAll($sesi),
                'random_by_jenis' => $this->handleRandomByJenis($sesi),
                'fixed_rule' => $this->handleFixedRule($sesi),
                default => throw ValidationException::withMessages([
                    'tipe_ujian_id' => 'Mode ujian tidak valid'
                ])
            };

            DB::commit();

            $this->alertSuccess('Berhasil', 'Sesi ujian diperbarui & soal di-regenerate!');
            return redirect()->route('admin.sesi-ujian.index');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->alertError('Error', $e->getMessage());
        }
    }

    // ============================================================
    // HANDLERS (sama seperti Create)
    // ============================================================

    private function handleRandomAll(SesiUjian $sesi)
    {
        $soals = Soal::inRandomOrder()
            ->limit($this->jumlah_soal)
            ->pluck('id');

        if ($soals->count() < $this->jumlah_soal) {
            throw ValidationException::withMessages([
                'jumlah_soal' => "Jumlah soal tidak mencukupi (tersedia: {$soals->count()})"
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
            // dd($soals);

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

    private function attachSoalsToSesi(SesiUjian $sesi, $soalIds)
    {
        $data = collect($soalIds)->map(fn($id) => [
            'sesi_ujian_id' => $sesi->id,
            'soal_id' => $id,
        ])->toArray();

        SesiSoal::insert($data);
    }

    // ============================================================
    // DYNAMIC FORM HELPERS
    // ============================================================

    public function updatedTipeUjianId($value)
    {
        $tipeUjian = \App\Models\TipeUjian::find($value);
        $this->mode = $tipeUjian?->getRawOriginal('mode');

        // Reset mode-specific inputs
        $this->jumlah_soal = null;
        $this->jenis_ujian_id = null;
        $this->komposisi = [];

        // Auto-add 1 row untuk fixed_rule
        if ($this->mode === 'fixed_rule') {
            $this->komposisi[] = [
                'jenis_ujian_id' => null,
                'jumlah_soal' => null,
            ];
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

    public function render()
    {
        $tipeUjian = \App\Models\TipeUjian::all();
        $jenisUjian = \App\Models\JenisUjian::all();
        return view('livewire.admin.sesi-ujian-edit', compact('tipeUjian', 'jenisUjian'));
    }
}
