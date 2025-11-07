<?php

namespace App\Livewire\Admin;

use App\Models\SesiUjian;
use App\Traits\HasAlert;
use Livewire\Component;
use Livewire\WithPagination;

class SesiUjianIndex extends Component
{
    use HasAlert, WithPagination;

    public $search = '';
    public $filterTipe = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFilterTipe()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        try {
            $sesi = SesiUjian::findOrFail($id);
            $sesi->delete();
            $this->alertSuccess('Berhasil', 'Sesi ujian dihapus!');
        } catch (\Exception $e) {
            $this->alertError('Error', 'Gagal menghapus sesi.');
        }
    }

    public function render()
    {
        $sesi = SesiUjian::with('tipeUjian')
            ->when($this->search, fn($q) => $q->where('judul', 'like', "%{$this->search}%"))
            ->when($this->filterTipe, fn($q) => $q->where('tipe_ujian_id', $this->filterTipe))
            ->latest()
            ->paginate(10);

        $tipeUjian = \App\Models\TipeUjian::all();

        return view('livewire.admin.sesi-ujian-index', [
            'sesi' => $sesi,
            'tipeUjian' => $tipeUjian,
        ]);
    }
}
