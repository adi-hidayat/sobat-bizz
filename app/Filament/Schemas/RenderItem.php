<?php

namespace App\Filament\Schemas;

use App\Enums\TaxType;
use App\Models\Product;
use App\Models\Tax;
use App\Services\Documents\DocumentTotalsCalculator;
use App\Support\Format;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;

/**
 * Renderer item tunggal untuk seluruh dokumen penjualan:
 * Proposal, Quotation, Incoming PO, Purchase Order, Delivery Order, Invoice.
 *
 * Data disimpan pada satu tabel `items` melalui relasi polymorphic, sehingga
 * penambahan/perubahan kolom item cukup dilakukan di sini.
 */
final class RenderItem
{
    public static function make(): Repeater
    {
        return Repeater::make('items')
            ->relationship()
            ->hiddenLabel()
            ->table([
                TableColumn::make(__('common.fields.product_service'))->width('220px'),
                TableColumn::make(__('common.fields.product_name'))->width('180px')->markAsRequired(),
                TableColumn::make(__('common.placeholders.qty'))->width('110px'),
                TableColumn::make(__('common.fields.unit_price'))->width('130px')->alignment(Alignment::End),
                TableColumn::make(__('common.fields.discount'))->width('110px')->alignment(Alignment::End),
                TableColumn::make(__('common.fields.tax'))->width('220px'),
                TableColumn::make(__('common.fields.line_total'))->width('140px')->alignment(Alignment::End),
            ])
            ->schema([
                Select::make('product_id')
                    ->label(__('common.fields.product_service'))
                    ->relationship('product', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Product $record): string => $record->display_name)
                    ->searchable(['sku', 'name'])
                    ->live()
                    ->afterStateUpdated(function (?int $state, Set $set): void {
                        $product = $state !== null ? Product::query()->find($state) : null;

                        if ($product === null) {
                            return;
                        }

                        $set('sku', $product->sku);
                        $set('name', $product->name);
                        $set('unit', $product->unit);
                        $set('unit_price', (float) $product->default_price);

                        if (blank($product->description) === false) {
                            $set('description', $product->description);
                        }
                    }),

                TextInput::make('name')
                    ->label(__('common.fields.product_name'))
                    ->required()
                    ->maxLength(255),

                Hidden::make('sku'),

                TextInput::make('quantity')
                    ->hiddenLabel()
                    ->placeholder(__('common.placeholders.qty'))
                    ->numeric()
                    ->minValue(0)
                    ->default(1)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateLineTotals($get, $set))
                    ->suffixAction(
                        Action::make('editUnit')
                            ->label(fn (Get $get): string => (string) ($get('unit') ?: __('common.fields.unit')))
                            ->link()
                            ->color('gray')
                            ->icon(Heroicon::PencilSquare)
                            ->iconPosition(IconPosition::After)
                            ->tooltip(__('common.actions.edit_unit'))
                            ->modalHeading(__('common.actions.edit_unit'))
                            ->modalSubmitActionLabel(__('common.actions.save'))
                            ->schema([
                                TextInput::make('unit')
                                    ->label(__('common.fields.unit'))
                                    ->required()
                                    ->maxLength(50),
                            ])
                            ->fillForm(fn (Get $get): array => ['unit' => (string) ($get('unit') ?? '')])
                            ->action(function (array $data, Set $set): void {
                                $set('unit', $data['unit']);
                            }),
                    ),

                TextInput::make('unit_price')
                    ->label(__('common.fields.unit_price'))
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateLineTotals($get, $set)),

                TextInput::make('discount_amount')
                    ->label(__('common.fields.discount'))
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateLineTotals($get, $set)),

                Select::make('tax_ids')
                    ->label(__('common.fields.tax'))
                    ->options(fn (): array => Tax::query()
                        ->orderByDesc('is_active')
                        ->ordered()
                        ->get()
                        ->mapWithKeys(fn (Tax $tax): array => [$tax->getKey() => self::taxLabel($tax)])
                        ->all())
                    ->default(function (): array {
                        $tax = self::defaultTax();

                        return $tax !== null ? [$tax->getKey()] : [];
                    })
                    ->placeholder(__('common.placeholders.no_tax'))
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->live()
                    ->afterStateUpdated(fn (?array $state, Get $get, Set $set) => self::updateLineTotals($get, $set, $state ?? [])),

                TextInput::make('total')
                    ->label(__('common.fields.line_total'))
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($state): string => Format::money($state)),

                Hidden::make('description'),
            ])
            ->defaultItems(1)
            ->addActionLabel(__('common.actions.add_item'))
            ->reorderable()
            ->orderColumn('sort_order')
            ->cloneable()
            ->columnSpanFull();
    }

    /**
     * @param  list<int|string>|null  $taxIds
     */
    private static function updateLineTotals(Get $get, Set $set, ?array $taxIds = null): void
    {
        $taxIds = array_values(array_filter($taxIds ?? (array) $get('tax_ids')));
        $taxes = Tax::query()->whereKey($taxIds)->get()
            ->keyBy(fn (Tax $tax): int => (int) $tax->getKey());

        $snapshots = [];

        foreach ($taxIds as $sortOrder => $taxId) {
            $tax = $taxes->get((int) $taxId);

            if ($tax === null) {
                continue;
            }

            $snapshots[] = [
                'type' => $tax->type,
                'value' => (float) $tax->value,
                'sort_order' => $sortOrder,
            ];
        }

        $totals = app(DocumentTotalsCalculator::class)->lineTotals(
            (float) $get('quantity'),
            (float) $get('unit_price'),
            (float) $get('discount_amount'),
            0,
            $snapshots,
        );

        $set('subtotal', $totals['subtotal']);
        $set('tax_amount', $totals['tax_amount']);
        $set('total', $totals['total']);
    }

    private static function taxLabel(Tax $tax): string
    {
        $value = $tax->type === TaxType::Percentage
            ? Format::quantity($tax->value).'%'
            : Format::money($tax->value);
        $label = "{$tax->name} ({$value})";

        return $tax->is_active
            ? $label
            : __('resources/tax.options.inactive', ['name' => $label]);
    }

    private static function defaultTax(): ?Tax
    {
        return once(fn (): ?Tax => Tax::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->ordered()
            ->first());
    }
}
