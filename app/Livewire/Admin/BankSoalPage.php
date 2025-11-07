<?php

namespace App\Livewire\Admin;

use App\Models\Soal;
use App\Models\JenisUjian;
use App\Traits\HasAlert;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class BankSoalPage extends Component
{
    use HasAlert, WithPagination;

    // Filter & Search
    public $search = '';
    public $filterJenis = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterJenis()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->alertConfirm(
            'Hapus Soal?',
            'Soal dan semua opsi akan dihapus permanen!',
            'delete',
            ['id' => $id]
        );
    }

    public function delete($id)
    {
        try {
            $id = $id['id'];
            // Pastikan ID valid
            // dd($id);
            if (!$id || !is_numeric($id)) {
                $this->alertError('Error', 'ID soal tidak valid!');
                return;
            }

            // Ambil soal + opsi
            $soal = Soal::with('opsi')->find($id);

            if (!$soal) {
                $this->alertError('Error', 'Soal tidak ditemukan!');
                return;
            }

            // === HAPUS FILE SOAL ===
            if ($soal->media_path && Storage::disk('public')->exists($soal->media_path)) {
                Storage::disk('public')->delete($soal->media_path);
            }

            // === HAPUS FILE OPSI (PAKSA COLLECTION) ===
            $opsiCollection = $soal->opsi ?? collect(); // PAKSA JADI COLLECTION!

            foreach ($opsiCollection as $opsi) {
                if ($opsi && $opsi->media_path && Storage::disk('public')->exists($opsi->media_path)) {
                    Storage::disk('public')->delete($opsi->media_path);
                }
            }

            // === HAPUS DARI DB ===
            $soal->delete();

            $this->alertSuccess('Berhasil', 'Soal berhasil dihapus!');
            $this->resetPage();
        } catch (\Exception $e) {
            Log::error('Delete soal error: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            $this->alertError('Error', 'Gagal menghapus soal: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $jenisUjian = JenisUjian::all();

        $soals = Soal::with(['jenis', 'opsi'])
            ->when($this->search, fn($q) => $q->where('pertanyaan_text', 'like', "%{$this->search}%"))
            ->when($this->filterJenis, fn($q) => $q->where('jenis_id', $this->filterJenis))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.bank-soal-page', [
            'soals' => $soals,
            'jenisUjian' => $jenisUjian,
        ]);
    }
}
