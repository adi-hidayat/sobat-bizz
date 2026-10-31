<?php

namespace App\Filament\Resources\Staff\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StaffInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resources/staff.sections.employment'))
                    ->schema([
                        TextEntry::make('employee_number')->label(__('resources/staff.fields.employee_number')),
                        TextEntry::make('department.name')->label(__('resources/staff.fields.department'))->placeholder('-'),
                        TextEntry::make('joined_at')
                            ->label(__('resources/staff.fields.joined_at'))
                            ->date('d M Y')
                            ->placeholder('-'),
                    ])
                    ->columns(3),

                Section::make(__('resources/staff.sections.account'))
                    ->schema([
                        TextEntry::make('user.name')->label(__('common.fields.name')),
                        TextEntry::make('user.email')->label(__('common.fields.email')),
                        TextEntry::make('user.phone')->label(__('common.fields.phone'))->placeholder('-'),
                        TextEntry::make('user.jobTitle.name')->label(__('common.fields.job_title'))->placeholder('-'),
                        TextEntry::make('user.roles.name')->label(__('common.fields.roles'))->badge(),
                        TextEntry::make('user.is_active')
                            ->label(__('common.fields.status'))
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state
                                ? __('resources/staff.status.active')
                                : __('resources/staff.status.inactive'))
                            ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                    ])
                    ->columns(3),
            ]);
    }
}
