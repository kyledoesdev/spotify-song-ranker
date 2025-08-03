<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class CreateAction extends GeneratorCommand
{
    protected $name = 'make:action';

    protected $description = 'Create a new Action class';

    protected $type = 'Action';

    protected function getStub()
    {
        return base_path('stubs/action.stub');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Actions';
    }
}
