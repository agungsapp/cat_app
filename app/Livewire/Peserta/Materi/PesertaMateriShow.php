<?php

namespace App\Livewire\Peserta\Materi;

use App\Models\Materi;
use App\Models\UserMateriProgress;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PesertaMateriShow extends Component
{
    public $materi;

    public function mount(Materi $materi)
    {
        $this->materi = $materi->load('topik', 'submateris.kontens');
    }

    public function render()
    {
        $userId = Auth::id();

        $totalKonten = 0;
        $completedKonten = 0;

        foreach ($this->materi->submateris as $submateri) {
            foreach ($submateri->kontens as $konten) {
                $totalKonten++;
                $isCompleted = UserMateriProgress::isCompleted($userId, $konten->id);
                if ($isCompleted) {
                    $completedKonten++;
                }
                // Tambahkan status ke konten
                $konten->is_completed = $isCompleted;
            }
        }

        $progress = $totalKonten > 0 ? round(($completedKonten / $totalKonten) * 100) : 0;

        return view('livewire.peserta.materi.peserta-materi-show', [
            'materi' => $this->materi,
            'total_konten' => $totalKonten,
            'completed_konten' => $completedKonten,
            'progress' => $progress,
        ])->title($this->materi->judul);
    }
}
