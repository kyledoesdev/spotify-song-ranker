<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class RankingForm extends Form
{
    #[Validate('string|required|max:30')]
    public string $name = '';

    #[Validate('required')]
    public $is_public = true;
}
