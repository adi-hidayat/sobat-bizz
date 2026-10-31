<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resources/user.sections.user_data'))
                    ->schema([
                        TextEntry::make('name')->label(__('common.fields.name')),
                        TextEntry::make('email')->label(__('common.fields.email')),
                        TextEntry::make('job_title')->label(__('common.fields.job_title'))->placeholder('-'),
                        TextEntry::make('phone')->label(__('common.fields.phone'))->placeholder('-'),
                        TextEntry::make('roles.name')->label(__('common.fields.roles'))->badge(),
                        TextEntry::make('is_active')
                            ->label(__('common.fields.status'))
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state
                                ? __('resources/user.status.active')
                                : __('resources/user.status.inactive'))
                            ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                    ])
                    ->columns(3),
            ]);
    }
}
