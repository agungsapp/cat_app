<?php

namespace App\Livewire\Peserta\Materi;

use App\Models\Konten;
use App\Models\UserMateriProgress;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PesertaKontenShow extends Component
{
    public $konten;
    public $materi;
    public $isCompleted = false;
    public $pdfUrl = null;

    public function mount(Konten $konten)
    {
        $this->konten = $konten->load('submateri.materi.topik');
        $this->materi = $this->konten->submateri->materi;

        $userId = Auth::id();
        $this->isCompleted = UserMateriProgress::isCompleted($userId, $konten->id);
        if (!$this->isCompleted) {
            UserMateriProgress::markCompleted($userId, $konten->id);
            $this->isCompleted = true;
            $this->dispatch('progressUpdated');
        }

        if ($this->konten->tipe === 'pdf') {
            $this->pdfUrl = route('livewire.pdf-stream', $this->konten->id);
        }
    }

    public function render()
    {
        return view('livewire.peserta.materi.peserta-konten-show');
    }
}
