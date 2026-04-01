<?php

namespace App\Livewire\Welcome;

use App\Models\LandingPageContent;
use Livewire\Component;

class Welcome extends Component
{
    public function render()
    {
        return view('livewire.welcome.welcome', [
            'content' => cache()->remember(
                'landing-page-contents',
                now()->addDay(),
                fn () => LandingPageContent::all()
            )
        ]);
    }
}
