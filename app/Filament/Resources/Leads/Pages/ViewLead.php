<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Actions\ConvertLeadToCustomer;
use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('convert')
                ->label(__('resources/lead.actions.convert'))
                ->icon('heroicon-o-arrow-right-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalDescription(__('resources/lead.actions.convert_description'))
                ->visible(fn (Lead $record): bool => $record->isConvertible())
                ->action(function (Lead $record, Action $action): void {
                    $customer = app(ConvertLeadToCustomer::class)->handle($record);

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
