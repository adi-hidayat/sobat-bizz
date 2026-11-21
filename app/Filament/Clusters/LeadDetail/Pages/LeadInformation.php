<?php

namespace App\Filament\Clusters\LeadDetail\Pages;

use App\Filament\Resources\Leads\Schemas\LeadInfolist;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadInformation extends LeadDetailPage
{
    protected static ?string $slug = 'information';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('pages.lead_detail.information');
    }

    public function getTitle(): string
    {
        return __('pages.lead_detail.information');
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->leadRecord)
            ->components([
                ...LeadInfolist::information(),
                Section::make(__('resources/lead.sections.address'))
                    ->schema(LeadInfolist::addressEntries())
                    ->columns(2),
            ]);
    }
}
