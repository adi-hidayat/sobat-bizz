<?php

namespace App\Filament\Resources\Departments\Schemas;

use App\Models\Department;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DepartmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label(__('resources/department.fields.name')),
                TextEntry::make('staff_count')
                    ->label(__('resources/department.fields.staff_count'))
                    ->state(fn (Department $record): int => $record->staff()->count()),
                TextEntry::make('sort_order')->label(__('common.fields.sort_order')),
                TextEntry::make('is_active')
                    ->label(__('common.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('resources/department.status.active') : __('resources/department.status.inactive'))
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                TextEntry::make('created_at')->label(__('common.fields.created_at'))->dateTime('d M Y H:i'),
            ]);
    }
}
