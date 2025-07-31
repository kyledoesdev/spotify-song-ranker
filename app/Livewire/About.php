<?php

namespace App\Livewire;

use App\Models\ApplicationDashboard;
use Livewire\Component;

class About extends Component
{
    public function render()
    {
        return view('livewire.about', [
            'aboutPage' => ApplicationDashboard::first()->about_page,
        ]);
    }
}
