<?php

namespace App\Filament\Clusters\LeadDetail\Pages;

use App\Filament\Resources\Leads\RelationManagers\ActivitiesRelationManager;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;

class LeadActivities extends LeadDetailPage
{
    protected static ?string $slug = 'activities';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('pages.lead_detail.activities');
    }

    public function getTitle(): string
    {
        return __('pages.lead_detail.activities');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Livewire::make(ActivitiesRelationManager::class, [
                'ownerRecord' => $this->leadRecord,
                'pageClass' => static::class,
            ])->key('activities'),
        ]);
    }
}
