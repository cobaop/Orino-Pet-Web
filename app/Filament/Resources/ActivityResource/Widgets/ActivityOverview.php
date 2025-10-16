<?php

namespace App\Filament\Resources\ActivityResource\Widgets;

use Filament\Widgets\Widget;

class ActivityOverview extends Widget
{
    protected static string $view = 'filament.resources.activity-resource.widgets.activity-overview';

    public $record;

    public function mount($record): void
    {
        $this->record = $record;
    }

    public static function canView(): bool
    {
        return true;
    }
}
