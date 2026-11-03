<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Actions\ConvertLeadToCustomer;
use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Filament\Actions\TableActions;
use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Support\UserAvatar;
use App\Models\Lead;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                TextColumn::make('display_name')
                    ->label(__('resources/lead.fields.lead'))
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->where(
                        fn (Builder $query): Builder => $query
                            ->where('company_name', 'like', "%{$search}%")
                            ->orWhere('contact_person', 'like', "%{$search}%")
                            ->orWhere('lead_number', 'like', "%{$search}%"),
                    ))
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderByRaw(
                        'coalesce(company_name, contact_person) '.($direction === 'desc' ? 'desc' : 'asc'),
                    )),
                TextColumn::make('lead_type')
                    ->label(__('resources/lead.fields.lead_type'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('job_title')
                    ->label(__('common.fields.job_title'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('rating.name')
                    ->label(__('resources/lead.fields.rating'))
                    ->formatStateUsing(fn (?string $state, Lead $record): string => view(
                        'filament.partials.lead-rating-badge',
                        ['rating' => $record->rating],
                    )->render())
                    ->html(),
                TextColumn::make('estimated_value')
                    ->label(__('resources/lead.fields.estimated_value'))
                    ->money('IDR')
                    ->prefix('~')
                    ->sortable()
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
                TextColumn::make('salesTeam.name')
                    ->label(__('resources/lead.fields.sales_team'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('follow_up_date')
                    ->label(__('resources/lead.fields.follow_up'))
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn (Lead $record): ?string => $record->follow_up_date?->isPast() ? 'danger' : null),
                TextColumn::make('last_contacted_at')
                    ->label(__('resources/lead.fields.last_contacted_at'))
                    ->dateTime('d M Y H:i')
                    ->placeholder(__('common.placeholders.none'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('common.fields.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('lead_type')
                    ->label(__('resources/lead.fields.lead_type'))
                    ->options(LeadType::class),
                SelectFilter::make('lead_rating_id')
                    ->label(__('resources/lead.fields.rating'))
                    ->relationship('rating', 'name'),
                SelectFilter::make('status')
                    ->label(__('common.fields.status'))
                    ->options(LeadStatus::class),
                SelectFilter::make('lead_source_id')
                    ->label(__('resources/lead.fields.source'))
                    ->relationship('leadSource', 'name'),
                Filter::make('assigned_to')
                    ->label(__('resources/lead.fields.sales'))
                    ->form([
                        Select::make('value')
                            ->hiddenLabel()
                            ->placeholder(__('resources/lead.fields.sales'))
                            ->options(fn (): array => User::query()
                                ->with('staff')
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(fn (User $user): array => [
                                    $user->getKey() => UserAvatar::optionLabel($user),
                                ])
                                ->all())
                            ->searchable()
                            ->allowHtml(),
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (blank($data['value'] ?? null)) {
                            return null;
                        }

                        return __('resources/lead.fields.sales').': '.(User::query()->whereKey($data['value'])->value('name') ?? $data['value']);
                    })
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $query): Builder => $query->where(
                            fn (Builder $query): Builder => $query
                                ->where('assigned_to', $data['value'])
                                ->orWhereHas(
                                    'salesTeam',
                                    fn (Builder $query): Builder => $query->whereKey($data['value']),
                                ),
                        ),
                    )),
                Filter::make('overdue_follow_up')
                    ->label(__('resources/lead.filters.overdue_follow_up'))
                    ->query(fn (Builder $query): Builder => $query->overdueFollowUp()),
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
