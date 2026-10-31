<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Actions\ConvertLeadToCustomer;
use App\Enums\LeadStatus;
use App\Filament\Actions\TableActions;
use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lead_number')
                    ->label(__('common.fields.number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company_name')
                    ->label(__('resources/lead.fields.company'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contact_person')
                    ->label(__('resources/lead.fields.contact_person'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('leadSource.name')
                    ->label(__('resources/lead.fields.source'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('common.fields.status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('assignedSalesPerson.name')
                    ->label(__('resources/lead.fields.sales'))
                    ->toggleable(),
                TextColumn::make('follow_up_date')
                    ->label(__('resources/lead.fields.follow_up'))
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn (Lead $record): ?string => $record->follow_up_date?->isPast() ? 'danger' : null),
                TextColumn::make('created_at')
                    ->label(__('common.fields.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('common.fields.status'))
                    ->options(LeadStatus::class),
                SelectFilter::make('lead_source_id')
                    ->label(__('resources/lead.fields.source'))
                    ->relationship('leadSource', 'name'),
                SelectFilter::make('assigned_to')
                    ->label(__('resources/lead.fields.sales'))
                    ->relationship('assignedSalesPerson', 'name'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                TableActions::group([
                    ViewAction::make(),
                    TableActions::detail(fn (Lead $record): string => LeadResource::getUrl('view', ['record' => $record])),
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
                ]),
            ])
            ->recordUrl(null)
            ->recordAction('view')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
