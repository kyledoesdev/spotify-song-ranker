<?php

namespace App\Filament\Resources\ApplicationDashboards\Pages;

use App\Filament\Resources\ApplicationDashboards\ApplicationDashboardResource;
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
