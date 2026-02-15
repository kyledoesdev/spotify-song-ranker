<?php

namespace App\Livewire;

use App\Models\Faq as FaqModel;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Faq extends Component
{
    #[Computed]
    public function faqs(): Collection
    {
        return FaqModel::query()->active()->get();
    }

    public function render()
    {
        return view('livewire.faq');
    }
}
