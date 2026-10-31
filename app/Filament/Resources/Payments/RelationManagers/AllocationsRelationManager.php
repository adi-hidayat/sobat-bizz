<?php

namespace App\Filament\Resources\Payments\RelationManagers;

use App\Filament\Actions\TableActions;
use App\Models\Invoice;
use App\Models\Payment;
use App\Support\Format;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AllocationsRelationManager extends RelationManager
{
    protected static string $relationship = 'allocations';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('resources/payment.relation_managers.allocations.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('invoice_id')
                    ->label(__('resources/payment.relation_managers.allocations.invoice'))
                    ->options(function (): array {
                        /** @var Payment $payment */
                        $payment = $this->getOwnerRecord();

                        return Invoice::query()
                            ->where('customer_id', $payment->customer_id)
                            ->whereNotIn('status', ['draft', 'cancelled'])
                            ->where('outstanding_balance', '>', 0)
                            ->orderBy('invoice_date')
                            ->get()
                            ->mapWithKeys(fn (Invoice $invoice): array => [
                                $invoice->getKey() => __('resources/payment.relation_managers.allocations.option', [
                                    'number' => $invoice->invoice_number,
                                    'outstanding' => Format::money($invoice->outstanding_balance),
                                ]),
                            ])
                            ->all();
                    })
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (?int $state, Set $set): void {
                        if ($state === null) {
                            return;
                        }

                        $invoice = Invoice::query()->find($state);

                        if ($invoice !== null) {
                            $set('amount', (float) $invoice->outstanding_balance);
                        }
                    }),
                TextInput::make('amount')
                    ->label(__('resources/payment.relation_managers.allocations.amount'))
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice.invoice_number')->label(__('resources/payment.relation_managers.allocations.invoice'))->searchable(),
                TextColumn::make('invoice.customer.company_name')->label(__('common.fields.customer'))->toggleable(),
                TextColumn::make('invoice.due_date')->label(__('common.fields.due_date'))->date('d M Y'),
                TextColumn::make('amount')
                    ->label(__('resources/payment.relation_managers.allocations.allocation'))
                    ->formatStateUsing(fn ($state): string => Format::money($state)),
                TextColumn::make('created_at')->label(__('common.fields.created_at'))->dateTime('d M Y H:i')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make()->label(__('resources/payment.relation_managers.allocations.add')),
            ])
            ->recordActions([
                TableActions::group([
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
