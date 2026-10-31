<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resources/user.sections.user_data'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('common.fields.name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('common.fields.email'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('job_title')
                            ->label(__('common.fields.job_title'))
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label(__('common.fields.phone'))
                            ->tel()
                            ->maxLength(50),
                        TextInput::make('password')
                            ->label(__('common.fields.password'))
                            ->password()
                            ->revealable()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->helperText(__('resources/user.form.password_helper')),
                        Toggle::make('is_active')
                            ->label(__('common.fields.is_active'))
                            ->default(true),
                        Select::make('roles')
                            ->label(__('common.fields.roles'))
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(2),
            ]);
    }
}
