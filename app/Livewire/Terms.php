<?php

namespace App\Livewire;

use App\Models\Terms as TermsModel;
use Livewire\Component;

class Terms extends Component
{
    public function render()
    {
        return view('livewire.terms', [
            'content' => TermsModel::latest()->first()->content,
        ]);
    }
}
