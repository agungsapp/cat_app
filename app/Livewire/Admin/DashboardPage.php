<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardPage extends Component
{
    public function render()
    {

        // dd(Auth::user()->role);

        return view('livewire.admin.dashboard-page');
    }
}
