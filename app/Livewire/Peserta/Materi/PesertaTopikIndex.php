<?php

namespace App\Livewire\Peserta\Materi;

use App\Models\Topik;
use App\Models\UserMateriProgress;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PesertaTopikIndex extends Component
{
    public $search = '';

    public function render()
    {
        $userId = Auth::id();

        $topiks = Topik::with(['materis.submateris.kontens'])
            ->when($this->search, function ($query) {
                $query->where('nama_topik', 'like', "%{$this->search}%")
                    ->orWhereHas('materis', function ($q) {
                        $q->where('judul', 'like', "%{$this->search}%");
                    });
            })
            ->orderBy('urutan')
            ->get();

        foreach ($topiks as $topik) {
            $totalKonten = 0;
            $completedKonten = 0;

            foreach ($topik->materis as $materi) {
                foreach ($materi->submateris as $submateri) {
                    foreach ($submateri->kontens as $konten) {
                        $totalKonten++;
                        if (UserMateriProgress::isCompleted($userId, $konten->id)) {
                            $completedKonten++;
                        }
                    }
                }
            }

            $topik->total_konten     = $totalKonten;
            $topik->completed_konten = $completedKonten;
            $topik->progress         = $totalKonten > 0 ? round(($completedKonten / $totalKonten) * 100) : 0;
        }

        return view('livewire.peserta.materi.peserta-topik-index', [
            'topiks' => $topiks
        ])->title('Materi Pembelajaran');
    }
}
