<?php

namespace App\Filament\Resources\ApplicationDashboardResource\Pages;

use App\Filament\Resources\ApplicationDashboardResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageApplicationDashboards extends ManageRecords
{
    protected static string $resource = ApplicationDashboardResource::class;

    public function getHeading(): string
    {
        return 'Application Settings';
    }

    public function getTitle(): string
    {
        return 'Application Settings';
    }
}
