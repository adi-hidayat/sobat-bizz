<?php

namespace App\Services\Documents;

use App\Enums\DocumentType;
use App\Models\DocumentSequence;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

final class DocumentNumberGenerator
{
    public function generate(DocumentType $type, ?CarbonInterface $date = null): string
    {
        $date ??= now();

        $sequence = $this->nextSequence($type, $date);

        return $this->format($type, $date, $sequence);
    }

    private function nextSequence(DocumentType $type, CarbonInterface $date): int
    {
        return DB::transaction(function () use ($type, $date): int {
            DocumentSequence::query()->firstOrCreate(
                ['document_type' => $type->value, 'year' => (int) $date->year],
                ['last_number' => 0],
            );

            /** @var DocumentSequence $sequence */
            $sequence = DocumentSequence::query()
                ->where('document_type', $type->value)
                ->where('year', (int) $date->year)
                ->lockForUpdate()
                ->firstOrFail();

            $next = $sequence->last_number + 1;

            $sequence->forceFill(['last_number' => $next])->save();

            return $next;
        });
    }

    private function format(DocumentType $type, CarbonInterface $date, int $sequence): string
    {
        $number = str_replace(
            ['{prefix}', '{year}', '{month}', '{day}'],
            [$type->prefix(), $date->format('Y'), $date->format('m'), $date->format('d')],
            $type->pattern(),
        );

        return (string) preg_replace_callback(
            '/\{sequence(?::(\d+))?\}/',
            fn (array $matches): string => str_pad((string) $sequence, (int) ($matches[1] ?? 4), '0', STR_PAD_LEFT),
            $number,
        );
    }

    /**
     * Nomor berikutnya tanpa menaikkan counter — untuk keperluan preview.
     */
    public function preview(DocumentType $type, ?CarbonInterface $date = null): string
    {
        $date ??= now();

        $lastNumber = DocumentSequence::query()
            ->where('document_type', $type->value)
            ->where('year', (int) $date->year)
            ->value('last_number');

        return $this->format($type, $date, (int) $lastNumber + 1);
    }
}
