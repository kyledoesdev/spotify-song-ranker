<?php

namespace App\Livewire;

use App\Models\ApplicationDashboard;
use Livewire\Component;

class Support extends Component
{
    public function render()
    {
        return view('livewire.support', [
            'content' => ApplicationDashboard::first()->support_page,
        ]);
    }
}
