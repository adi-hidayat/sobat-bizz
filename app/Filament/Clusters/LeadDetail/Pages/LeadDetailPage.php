<?php

namespace App\Filament\Clusters\LeadDetail\Pages;

use App\Actions\ConvertLeadToCustomer;
use App\Filament\Clusters\LeadDetailCluster;
use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Schema;

abstract class LeadDetailPage extends Page
{
    protected static ?string $cluster = LeadDetailCluster::class;

    public Lead $leadRecord;

    public function mount(string $lead): void
    {
        $this->leadRecord = Lead::withTrashed()->findOrFail($lead);
    }

    public static function getRoutePath(Panel $panel): string
    {
        return '/{lead}/'.static::getSlug($panel);
    }

    public static function getNavigationUrl(): string
    {
        $lead = request()->route('lead') ?? request()->integer('lead');

        return filled($lead)
            ? static::getUrl(['lead' => $lead])
            : '#';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('view_any_lead') ?? false;
    }

    public function getTitle(): string
    {
        return (string) static::getNavigationLabel();
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            EmbeddedSchema::make('infolist'),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->label(__('common.actions.edit'))
                ->icon('heroicon-o-pencil-square')
                ->url(fn (): string => LeadResource::getUrl('edit', ['record' => $this->leadRecord])),
            Action::make('convert')
                ->label(__('resources/lead.actions.convert'))
                ->icon('heroicon-o-arrow-right-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalDescription(__('resources/lead.actions.convert_description'))
                ->visible(fn (): bool => $this->leadRecord->isConvertible())
                ->action(function (Action $action): void {
                    $customer = app(ConvertLeadToCustomer::class)->handle($this->leadRecord);

                    Notification::make()
                        ->success()
                        ->title(__('resources/lead.notifications.converted_title'))
                        ->body(__('resources/lead.notifications.converted_body', ['customer_code' => $customer->customer_code]))
                        ->send();

                    $action->redirect(CustomerResource::getUrl('edit', ['record' => $customer]));
                }),
        ];
    }
}
