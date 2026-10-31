<?php

namespace App\Filament\Actions;

use App\Actions\ConvertIncomingPoToDeliveryOrder;
use App\Actions\ConvertIncomingPoToPurchaseOrder;
use App\Actions\ConvertProposalToQuotation;
use App\Actions\ConvertQuotationToIncomingPo;
use App\Actions\CreateInvoiceFromDocument;
use App\Actions\ReviseQuotation;
use App\Filament\Resources\DeliveryOrders\DeliveryOrderResource;
use App\Filament\Resources\IncomingPos\IncomingPoResource;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use App\Filament\Resources\Quotations\QuotationResource;
use App\Mail\DocumentMail;
use App\Models\DeliveryOrder;
use App\Models\IncomingPo;
use App\Models\Proposal;
use App\Models\Quotation;
use App\Models\Supplier;
use App\Services\Documents\DocumentPdfRenderer;
use App\Services\Documents\RefreshFulfillmentStatus;
use App\Services\Documents\ValidateAllocationQuantities;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class DocumentActions
{
    public static function convertToQuotation(): Action
    {
        return Action::make('convertToQuotation')
            ->label(__('documents.actions.convert_to_quotation'))
            ->icon('heroicon-o-document-duplicate')
            ->color('success')
            ->requiresConfirmation()
            ->modalDescription(__('documents.descriptions.convert_to_quotation'))
            ->visible(fn (Proposal $record): bool => $record->items()->exists()
                && ! in_array($record->status->value, ['cancelled', 'rejected'], true))
            ->action(function (Proposal $record, Action $action): void {
                try {
                    $quotation = app(ConvertProposalToQuotation::class)->handle($record);
                } catch (InvalidArgumentException $exception) {
                    Notification::make()->danger()->title($exception->getMessage())->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title(__('documents.notifications.quotation_created.title'))
                    ->body(__('documents.notifications.quotation_created.body', [
                        'number' => $quotation->quotation_number,
                    ]))
                    ->send();

                $action->redirect(QuotationResource::getUrl('edit', ['record' => $quotation]));
            });
    }

    public static function convertToIncomingPo(): Action
    {
        return Action::make('convertToIncomingPo')
            ->label(__('documents.actions.convert_to_incoming_po'))
            ->icon('heroicon-o-clipboard-document-list')
            ->color('success')
            ->schema([
                TextInput::make('customer_po_number')
                    ->label(__('documents.fields.customer_po_number'))
                    ->maxLength(100),
                DatePicker::make('po_date')
                    ->label(__('documents.fields.po_date'))
                    ->default(now())
                    ->required(),
                DatePicker::make('expected_delivery_date')
                    ->label(__('documents.fields.expected_delivery_date')),
            ])
            ->visible(fn (Quotation $record): bool => $record->items()->exists()
                && ! $record->status->isCancelled())
            ->action(function (Quotation $record, array $data, Action $action): void {
                try {
                    $incomingPo = app(ConvertQuotationToIncomingPo::class)->handle($record, $data);
                } catch (InvalidArgumentException $exception) {
                    Notification::make()->danger()->title($exception->getMessage())->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title(__('documents.notifications.incoming_po_created.title'))
                    ->body(__('documents.notifications.incoming_po_created.body', [
                        'number' => $incomingPo->incoming_po_number,
                    ]))
                    ->send();

                $action->redirect(IncomingPoResource::getUrl('edit', ['record' => $incomingPo]));
            });
    }

    public static function reviseQuotation(): Action
    {
        return Action::make('reviseQuotation')
            ->label(__('documents.actions.revise_quotation'))
            ->icon('heroicon-o-document-duplicate')
            ->color('warning')
            ->requiresConfirmation()
            ->modalDescription(__('documents.descriptions.revise_quotation'))
            ->visible(fn (Quotation $record): bool => $record->items()->exists()
                && ! $record->status->isCancelled())
            ->action(function (Quotation $record, Action $action): void {
                try {
                    $revision = app(ReviseQuotation::class)->handle($record);
                } catch (InvalidArgumentException $exception) {
                    Notification::make()->danger()->title($exception->getMessage())->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title(__('documents.notifications.quotation_revision_created.title'))
                    ->body(__('documents.notifications.quotation_revision_created.body', [
                        'number' => $revision->quotation_number,
                        'version' => $revision->version,
                    ]))
                    ->send();

                $action->redirect(QuotationResource::getUrl('edit', ['record' => $revision]));
            });
    }

    public static function convertToPurchaseOrder(): Action
    {
        return Action::make('convertToPurchaseOrder')
            ->label(__('documents.actions.convert_to_purchase_order'))
            ->icon('heroicon-o-shopping-cart')
            ->color('info')
            ->schema([
                Select::make('supplier_id')
                    ->label(__('documents.fields.supplier'))
                    ->options(fn (): array => Supplier::query()->active()->orderBy('company_name')->pluck('company_name', 'id')->all())
                    ->searchable()
                    ->required(),
                DatePicker::make('po_date')
                    ->label(__('documents.fields.po_date'))
                    ->default(now())
                    ->required(),
                DatePicker::make('expected_delivery_date')
                    ->label(__('documents.fields.expected_arrival')),
            ])
            ->visible(fn (IncomingPo $record): bool => $record->items()->exists()
                && ! $record->status->isCancelled())
            ->action(function (IncomingPo $record, array $data, Action $action): void {
                $supplier = Supplier::query()->findOrFail($data['supplier_id']);

                $purchaseOrder = app(ConvertIncomingPoToPurchaseOrder::class)->handle($record, $supplier, [
                    'po_date' => $data['po_date'],
                    'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                ]);

                Notification::make()
                    ->success()
                    ->title(__('documents.notifications.purchase_order_created.title'))
                    ->body(__('documents.notifications.purchase_order_created.body', [
                        'number' => $purchaseOrder->po_number,
                    ]))
                    ->send();

                $action->redirect(PurchaseOrderResource::getUrl('edit', ['record' => $purchaseOrder]));
            });
    }

    public static function convertToDeliveryOrder(): Action
    {
        return Action::make('convertToDeliveryOrder')
            ->label(__('documents.actions.convert_to_delivery_order'))
            ->icon('heroicon-o-truck')
            ->color('info')
            ->schema([
                DatePicker::make('delivery_date')
                    ->label(__('documents.fields.delivery_date'))
                    ->default(now())
                    ->required(),
                TextInput::make('delivery_method')
                    ->label(__('documents.fields.delivery_method'))
                    ->maxLength(255),
            ])
            ->visible(fn (IncomingPo $record): bool => $record->items()->exists()
                && ! $record->status->isCancelled())
            ->action(function (IncomingPo $record, array $data, Action $action): void {
                try {
                    $deliveryOrder = app(ConvertIncomingPoToDeliveryOrder::class)->handle($record, $data);
                } catch (InvalidArgumentException $exception) {
                    Notification::make()->danger()->title($exception->getMessage())->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title(__('documents.notifications.delivery_order_created.title'))
                    ->body(__('documents.notifications.delivery_order_created.body', [
                        'number' => $deliveryOrder->do_number,
                    ]))
                    ->send();

                $action->redirect(DeliveryOrderResource::getUrl('edit', ['record' => $deliveryOrder]));
            });
    }

    public static function createInvoiceFromIncomingPo(): Action
    {
        return Action::make('createInvoice')
            ->label(__('documents.actions.create_invoice'))
            ->icon('heroicon-o-receipt-percent')
            ->color('warning')
            ->schema(self::invoiceSchema())
            ->visible(fn (IncomingPo $record): bool => $record->items()->exists()
                && ! $record->status->isCancelled())
            ->action(function (IncomingPo $record, array $data, Action $action): void {
                self::handleInvoiceCreation($record, $data, $action);
            });
    }

    public static function createInvoiceFromDeliveryOrder(): Action
    {
        return Action::make('createInvoice')
            ->label(__('documents.actions.create_invoice'))
            ->icon('heroicon-o-receipt-percent')
            ->color('warning')
            ->schema(self::invoiceSchema())
            ->visible(fn (DeliveryOrder $record): bool => $record->items()->exists()
                && ! $record->status->isCancelled())
            ->action(function (DeliveryOrder $record, array $data, Action $action): void {
                self::handleInvoiceCreation($record, $data, $action);
            });
    }

    /**
     * Unduh dokumen sebagai PDF.
     */
    public static function downloadPdf(): Action
    {
        return Action::make('downloadPdf')
            ->label(__('documents.actions.download_pdf'))
            ->icon('heroicon-o-arrow-down-tray')
            ->action(function ($record): StreamedResponse {
                $renderer = app(DocumentPdfRenderer::class);

                return response()->streamDownload(
                    fn () => print $renderer->render($record)->output(),
                    $renderer->filename($record),
                    ['Content-Type' => 'application/pdf'],
                );
            });
    }

    /**
     * Kirim dokumen via email beserta lampiran PDF (dijalankan lewat queue).
     */
    public static function sendEmail(): Action
    {
        return Action::make('sendEmail')
            ->label(__('documents.actions.send_email'))
            ->icon('heroicon-o-envelope')
            ->schema([
                TextInput::make('email')
                    ->label(__('documents.fields.email_to'))
                    ->email()
                    ->required()
                    ->default(fn ($record): ?string => DocumentMail::recipientFor($record)),
                TextInput::make('subject')
                    ->label(__('documents.fields.subject'))
                    ->maxLength(255)
                    ->placeholder(__('common.placeholders.follow_document_number')),
                Textarea::make('message')
                    ->label(__('documents.fields.message'))
                    ->rows(4)
                    ->default(fn (): string => __('documents.mail.default_message')),
            ])
            ->action(function ($record, array $data): void {
                Mail::to($data['email'])->queue(new DocumentMail($record, $data['message'], app()->getLocale()));

                Notification::make()
                    ->success()
                    ->title(__('documents.notifications.email_scheduled.title'))
                    ->body(__('documents.notifications.email_scheduled.body', [
                        'email' => $data['email'],
                    ]))
                    ->send();
            });
    }

    /**
     * Action generik untuk mengubah status dokumen yang sudah terbit.
     *
     * @param  array<string, string>|class-string<\BackedEnum>  $statuses
     */
    public static function changeStatus(array|string $statuses, string $permission, ?callable $beforeUpdate = null): Action
    {
        return Action::make('changeStatus')
            ->label(__('documents.actions.change_status'))
            ->icon('heroicon-o-arrow-path')
            ->schema([
                Select::make('status')
                    ->label(__('documents.fields.status_new'))
                    ->options($statuses)
                    ->required(),
            ])
            ->fillForm(fn ($record): array => ['status' => $record->status->value])
            ->visible(fn ($record): bool => ! $record->status->isCancelled() && auth()->user()->can($permission))
            ->action(function ($record, array $data, Action $action) use ($beforeUpdate): void {
                $previous = $record->status;

                $record->forceFill(['status' => $data['status']])->save();

                if ($beforeUpdate !== null) {
                    try {
                        $beforeUpdate($record, $previous);
                    } catch (Throwable $exception) {
                        $record->forceFill(['status' => $previous])->save();

                        Notification::make()
                            ->danger()
                            ->title(__('documents.notifications.status_failed.title'))
                            ->body($exception->getMessage())
                            ->send();

                        return;
                    }
                }

                Notification::make()
                    ->success()
                    ->title(__('documents.notifications.status_updated.title'))
                    ->body(__('documents.notifications.status_updated.body', [
                        'status' => $record->status->getLabel(),
                    ]))
                    ->send();

                if ($record instanceof DeliveryOrder) {
                    app(RefreshFulfillmentStatus::class)->forDeliveryOrder($record);
                }
            });
    }

    /**
     * @return array<int, mixed>
     */
    private static function invoiceSchema(): array
    {
        return [
            DatePicker::make('invoice_date')
                ->label(__('documents.fields.invoice_date'))
                ->default(now())
                ->required(),
            DatePicker::make('due_date')
                ->label(__('documents.fields.due_date'))
                ->default(now()->addDays(30))
                ->required(),
            TextInput::make('payment_terms')
                ->label(__('documents.fields.payment_terms'))
                ->maxLength(255),
        ];
    }

    private static function handleInvoiceCreation(IncomingPo|DeliveryOrder $record, array $data, Action $action): void
    {
        try {
            $invoice = app(CreateInvoiceFromDocument::class)->handle($record, $data);
        } catch (InvalidArgumentException $exception) {
            Notification::make()->danger()->title($exception->getMessage())->send();

            return;
        }

        Notification::make()
            ->success()
            ->title(__('documents.notifications.invoice_created.title'))
            ->body(__('documents.notifications.invoice_created.body', [
                'number' => $invoice->invoice_number,
            ]))
            ->send();

        $action->redirect(InvoiceResource::getUrl('edit', ['record' => $invoice]));
    }

    /**
     * Validasi kuantitas sebelum dokumen turunan keluar dari Draft.
     */
    public static function validateQuantities(): callable
    {
        return function ($record, $previous): void {
            if ($record->status->value === 'draft') {
                return;
            }

            app(ValidateAllocationQuantities::class)->assertWithinSources($record);
        };
    }
}
