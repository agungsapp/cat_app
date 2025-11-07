<?php

namespace App\Livewire\Admin;

use App\Models\SesiUjian;
use App\Models\Soal;
use App\Traits\HasAlert;
use Livewire\Component;

class SesiUjianAssignSoal extends Component
{
    use HasAlert;

    public $sesiId;
    public $sesi;
    public $search = '';
    public $filterJenis = '';
    public $selectedSoal = [];

    public function mount($id)
    {
        $this->sesiId = $id;
        $this->sesi = SesiUjian::findOrFail($id);
        $this->loadSelected();
    }

    public function loadSelected()
    {
        $this->selectedSoal = $this->sesi->soal->pluck('id')->toArray();
    }

    public function save()
    {
        $this->sesi->soal()->sync($this->selectedSoal);
        $this->alertSuccess('Berhasil', 'Soal berhasil disimpan!');
    }

    public function render()
    {
        $soal = Soal::with('jenis')
            ->when($this->search, fn($q) => $q->where('pertanyaan_text', 'like', "%{$this->search}%"))
            ->when($this->filterJenis, fn($q) => $q->where('jenis_ujian_id', $this->filterJenis))
            ->get();

        $jenisUjian = \App\Models\JenisUjian::all();

        return view('livewire.admin.sesi-ujian-assign-soal', [
            'soal' => $soal,
            'jenisUjian' => $jenisUjian,
        ]);
    }
}
