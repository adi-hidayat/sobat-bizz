<?php

namespace App\Filament\Resources\Customers\Tables;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use App\Filament\Actions\TableActions;
use App\Filament\Clusters\CustomerDetailCluster;
use App\Models\Customer;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_code')
                    ->label(__('common.fields.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company_name')
                    ->label(__('resources/customer.fields.company'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_type')
                    ->label(__('common.fields.type'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('common.fields.status'))
                    ->badge(),
                TextColumn::make('email')
                    ->label(__('common.fields.email'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label(__('common.fields.phone'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payment_term_days')
                    ->label(__('resources/customer.fields.payment_term'))
                    ->suffix(__('resources/customer.fields.days_suffix'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('credit_limit')
                    ->label(__('common.fields.credit_limit'))
                    ->money('IDR')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('common.fields.created_at'))
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('common.fields.status'))
                    ->options(CustomerStatus::class),
                SelectFilter::make('customer_type')
                    ->label(__('common.fields.type'))
                    ->options(CustomerType::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                TableActions::group([
                    ViewAction::make(),
                    TableActions::detail(fn (Customer $record): string => CustomerDetailCluster::getUrl(['customer' => $record->getKey()])),
                    EditAction::make(),
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
            ->defaultSort('company_name');
    }
}
