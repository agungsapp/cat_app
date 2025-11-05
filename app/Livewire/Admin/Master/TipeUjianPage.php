<?php

namespace App\Livewire\Admin\Master;

use App\Traits\HasAlert;
use Livewire\Component;

class TipeUjianPage extends Component
{
    use HasAlert;

    public function render()
    {

        $this->alertSuccess("oke", "oke juga");
        return view('livewire.admin.master.tipe-ujian-page');
    }
}
