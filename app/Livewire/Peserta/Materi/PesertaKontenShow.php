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

    public function mount(Konten $konten)
    {
        // Load relasi
        $this->konten = $konten->load('submateri.materi.topik');
        $this->materi = $this->konten->submateri->materi;

        // Tandai selesai otomatis (sekali buka = selesai)
        $userId = Auth::id();
        $this->isCompleted = UserMateriProgress::isCompleted($userId, $konten->id);

        if (!$this->isCompleted) {
            UserMateriProgress::markCompleted($userId, $konten->id);
            $this->isCompleted = true;
            $this->dispatch('progressUpdated'); // buat refresh progress di halaman lain
        }
    }

    public function render()
    {
        return view('livewire.peserta.materi.peserta-konten-show', [
            'konten' => $this->konten,
            'materi' => $this->materi,
        ])->title($this->konten->isi ?? 'Konten Pembelajaran');
    }
}
