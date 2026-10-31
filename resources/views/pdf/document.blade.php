<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $number }}</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 11px; color: #1f2937; margin: 0; }
        .header { border-bottom: 2px solid #111827; padding-bottom: 10px; margin-bottom: 16px; }
        .company { font-size: 18px; font-weight: bold; letter-spacing: 1px; }
        .doc-title { font-size: 15px; font-weight: bold; text-transform: uppercase; margin-top: 4px; }
        .doc-number { font-size: 11px; color: #4b5563; }
        .meta-table { width: 100%; margin-bottom: 14px; }
        .meta-table td { vertical-align: top; padding: 2px 0; }
        .meta-label { color: #6b7280; width: 110px; }
        .box { border: 1px solid #e5e7eb; padding: 8px 10px; margin-bottom: 14px; }
        .box-title { font-size: 9px; text-transform: uppercase; color: #6b7280; margin-bottom: 3px; letter-spacing: 0.5px; }
        .box-name { font-weight: bold; font-size: 12px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.items th { background: #f3f4f6; text-align: left; padding: 6px; border-bottom: 1px solid #d1d5db; font-size: 10px; }
        table.items td { padding: 6px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .num { text-align: right; }
        .totals { width: 46%; margin-left: 54%; }
        .totals td { padding: 4px 6px; }
        .totals .grand { font-weight: bold; font-size: 13px; border-top: 1px solid #111827; }
        .section-title { font-size: 10px; text-transform: uppercase; color: #6b7280; margin: 12px 0 4px; letter-spacing: 0.5px; }
        .footer { position: fixed; bottom: -10px; left: 0; right: 0; font-size: 9px; color: #9ca3af; text-align: center; }
        .signature { width: 100%; margin-top: 36px; }
        .signature td { width: 50%; text-align: center; padding-top: 46px; border-top: 1px solid #d1d5db; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">{{ config('app.name') }}</div>
        <div class="doc-title">{{ $type->getLabel() }}</div>
        <div class="doc-number">{{ $number }}</div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 60%;">
                @if ($party)
                    <div class="box">
                        <div class="box-title">{{ $party['label'] }}</div>
                        <div class="box-name">{{ $party['name'] }}</div>
                        @if ($party['address'])
                            <div>{{ $party['address'] }}</div>
                        @endif
                        @if ($party['email'] || $party['phone'])
                            <div>{{ $party['email'] }} {{ $party['phone'] }}</div>
                        @endif
                    </div>
                @endif
            </td>
            <td style="width: 40%;">
                <table class="meta-table">
                    @foreach ($meta as $label => $value)
                        <tr>
                            <td class="meta-label">{{ $label }}</td>
                            <td>{{ $value }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    @if ($items->isNotEmpty())
        <table class="items">
            <thead>
                <tr>
                    <th style="width: 4%;">#</th>
                    <th style="width: 36%;">{{ __('documents.pdf.description') }}</th>
                    <th class="num" style="width: 8%;">{{ __('documents.pdf.qty') }}</th>
                    <th style="width: 8%;">{{ __('documents.pdf.unit') }}</th>
                    <th class="num" style="width: 14%;">{{ __('documents.pdf.price') }}</th>
                    <th class="num" style="width: 10%;">{{ __('documents.pdf.discount') }}</th>
                    <th class="num" style="width: 12%;">{{ __('documents.pdf.tax') }}</th>
                    <th class="num" style="width: 13%;">{{ __('documents.pdf.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->name }}</strong>
                            @if ($item->sku)
                                <div style="color: #6b7280; font-size: 9px;">{{ $item->sku }}</div>
                            @endif
                            @if ($item->description)
                                <div style="color: #6b7280; font-size: 9px;">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td class="num">{{ \App\Support\Format::quantity($item->quantity) }}</td>
                        <td>{{ $item->unit }}</td>
                        <td class="num">{{ \App\Support\Format::money($item->unit_price) }}</td>
                        <td class="num">{{ \App\Support\Format::money($item->discount_amount) }}</td>
                        <td class="num">
                            @forelse ($item->taxes as $itemTax)
                                {{ $itemTax->displayLabel() }}@unless ($loop->last)<br>@endunless
                            @empty
                                @if ((float) $item->tax_rate > 0)
                                    {{ \App\Support\Format::quantity($item->tax_rate) }}%
                                @else
                                    -
                                @endif
                            @endforelse
                        </td>
                        <td class="num">{{ \App\Support\Format::money($item->total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($hasTotals)
        <table class="totals">
            <tr>
                <td>{{ __('documents.pdf.subtotal') }}</td>
                <td class="num">{{ \App\Support\Format::money($document->subtotal) }}</td>
            </tr>
            <tr>
                <td>{{ __('documents.pdf.discount_document') }}</td>
                <td class="num">{{ \App\Support\Format::money($document->discount_amount) }}</td>
            </tr>
            <tr>
                <td>{{ __('documents.pdf.tax_total') }}</td>
                <td class="num">{{ \App\Support\Format::money($document->tax_amount) }}</td>
            </tr>
            <tr class="grand">
                <td>{{ __('documents.pdf.grand_total') }}</td>
                <td class="num">{{ \App\Support\Format::money($document->grand_total) }}</td>
            </tr>
            @if ($type === \App\Enums\DocumentType::Invoice)
                <tr>
                    <td>{{ __('documents.pdf.paid') }}</td>
                    <td class="num">{{ \App\Support\Format::money($document->amount_paid) }}</td>
                </tr>
                <tr>
                    <td>{{ __('documents.pdf.outstanding') }}</td>
                    <td class="num">{{ \App\Support\Format::money($document->outstanding_balance) }}</td>
                </tr>
            @endif
        </table>
    @endif

    @if ($terms)
        <div class="section-title">{{ __('documents.pdf.terms') }}</div>
        <div>{!! nl2br(e($terms)) !!}</div>
    @endif

    @if ($notes)
        <div class="section-title">{{ __('documents.pdf.notes') }}</div>
        <div>{!! nl2br(e($notes)) !!}</div>
    @endif

    <table class="signature">
        <tr>
            <td>{{ __('documents.pdf.prepared_by') }}</td>
            <td>{{ __('documents.pdf.approved_by') }}</td>
        </tr>
    </table>

    <div class="footer">
        {{ config('app.name') }} &ndash; {{ __('documents.pdf.footer') }}
    </div>
</body>
</html>
