<?php

namespace App\Filament\Resources\Staff\Tables;

use App\Filament\Actions\TableActions;
use App\Models\JobTitle;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class StaffTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label(__('resources/staff.fields.photo'))
                    ->circular()
                    ->height(40)
                    ->width(40),
                TextColumn::make('user.name')
                    ->label(__('common.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employee_number')
                    ->label(__('resources/staff.fields.employee_number'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('user.email')
                    ->label(__('common.fields.email'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('department.name')
                    ->label(__('resources/staff.fields.department'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('user.jobTitle.name')
                    ->label(__('common.fields.job_title'))
                    ->toggleable(),
                TextColumn::make('user.roles.name')
                    ->label(__('common.fields.roles'))
                    ->badge(),
                TextColumn::make('user.is_active')
                    ->label(__('common.fields.is_active'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state
                        ? __('resources/staff.status.active')
                        : __('resources/staff.status.inactive'))
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                TextColumn::make('joined_at')
                    ->label(__('resources/staff.fields.joined_at'))
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('common.fields.created_at'))
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label(__('common.fields.roles'))
                    ->options(fn (): array => Role::query()->orderBy('name')->pluck('name', 'name')->all())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $query): Builder => $query->whereHas(
                            'user.roles',
                            fn (Builder $query): Builder => $query->where('name', $data['value']),
                        ),
                    )),
                SelectFilter::make('department_id')
                    ->label(__('resources/staff.fields.department'))
                    ->relationship('department', 'name'),
                SelectFilter::make('job_title_id')
                    ->label(__('common.fields.job_title'))
                    ->options(fn (): array => JobTitle::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $query): Builder => $query->whereHas(
                            'user',
                            fn (Builder $query): Builder => $query->where('job_title_id', $data['value']),
                        ),
                    )),
                SelectFilter::make('is_active')
                    ->label(__('common.fields.status'))
                    ->options([
                        1 => __('resources/staff.status.active'),
                        0 => __('resources/staff.status.inactive'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $query): Builder => $query->whereHas(
                            'user',
                            fn (Builder $query): Builder => $query->where('is_active', (bool) $data['value']),
                        ),
                    )),
            ])
            ->recordActions([
                TableActions::group([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->recordUrl(null)
            ->recordAction('view')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['user', 'department']))
            ->defaultSort('employee_number');
    }
}
