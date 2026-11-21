<?php

namespace App\Filament\Clusters\LeadDetail\Pages;

use App\Filament\Resources\Leads\RelationManagers\ContactsRelationManager;
use App\Filament\Resources\Leads\Schemas\LeadInfolist;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadContact extends LeadDetailPage
{
    protected static ?string $slug = 'contact';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('pages.lead_detail.contact');
    }

    public function getTitle(): string
    {
        return __('pages.lead_detail.contact');
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->leadRecord)
            ->components([
                Section::make(__('resources/lead.sections.contact'))
                    ->schema(LeadInfolist::contactEntries())
                    ->columns(2),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            EmbeddedSchema::make('infolist'),
            Livewire::make(ContactsRelationManager::class, [
                'ownerRecord' => $this->leadRecord,
                'pageClass' => static::class,
            ])->key('contacts'),
        ]);
    }
}
