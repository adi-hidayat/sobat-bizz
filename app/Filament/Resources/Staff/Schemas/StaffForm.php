<?php

namespace App\Filament\Resources\Staff\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class StaffForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resources/staff.sections.employment'))
                    ->schema([
                        TextInput::make('employee_number')
                            ->label(__('resources/staff.fields.employee_number'))
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                        Select::make('department_id')
                            ->label(__('resources/staff.fields.department'))
                            ->relationship(
                                'department',
                                'name',
                                modifyQueryUsing: fn (Builder $query): Builder => $query->active(),
                            )
                            ->searchable()
                            ->preload(),
                        DatePicker::make('joined_at')
                            ->label(__('resources/staff.fields.joined_at')),
                        FileUpload::make('photo_path')
                            ->label(__('resources/staff.fields.photo'))
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('staff-photos')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make(__('resources/staff.sections.account'))
                    ->relationship('user')
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
                        Select::make('job_title_id')
                            ->label(__('common.fields.job_title'))
                            ->relationship(
                                'jobTitle',
                                'name',
                                modifyQueryUsing: fn (Builder $query): Builder => $query->active(),
                            )
                            ->searchable()
                            ->preload(),
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
                            ->helperText(__('resources/staff.form.password_helper')),
                        Toggle::make('is_active')
                            ->label(__('common.fields.is_active'))
                            ->default(true),
                        Select::make('roles')
                            ->label(__('common.fields.roles'))
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
