<?php

namespace App\Filament\Actions;

use App\Actions\RecordInvoicePayment;
use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Support\Format;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use InvalidArgumentException;

final class InvoiceActions
{
    public static function recordPayment(): Action
    {
        return Action::make('recordPayment')
            ->label(__('documents.actions.record_payment'))
            ->icon('heroicon-o-banknotes')
            ->color('success')
            ->schema([
                TextInput::make('amount')
                    ->label(__('documents.fields.amount'))
                    ->numeric()
                    ->default(fn (Invoice $record): float => (float) $record->outstanding_balance)
                    ->required(),
                DatePicker::make('payment_date')
                    ->label(__('documents.fields.payment_date'))
                    ->default(now())
                    ->required(),
                Select::make('method')
                    ->label(__('documents.fields.method'))
                    ->options(PaymentMethod::class)
                    ->default(PaymentMethod::BankTransfer)
                    ->required(),
                TextInput::make('reference')
                    ->label(__('documents.fields.reference'))
                    ->maxLength(255),
                Textarea::make('notes')
                    ->label(__('documents.fields.notes'))
                    ->rows(2),
            ])
            ->visible(fn (Invoice $record): bool => (float) $record->outstanding_balance > 0
                && ! $record->status->isCancelled()
                && auth()->user()->can('update_invoice'))
            ->action(function (Invoice $record, array $data): void {
                try {
                    $payment = app(RecordInvoicePayment::class)->handle($record, $data);
                } catch (InvalidArgumentException $exception) {
                    Notification::make()->danger()->title($exception->getMessage())->send();

                    return;
                }

                $record->refresh();

                Notification::make()
                    ->success()
                    ->title(__('documents.notifications.payment_recorded.title'))
                    ->body(__('documents.notifications.payment_recorded.body', [
                        'number' => $payment->payment_number,
                        'amount' => Format::money($payment->amount),
                        'outstanding' => Format::money($record->outstanding_balance),
                    ]))
                    ->send();
            });
    }
}
