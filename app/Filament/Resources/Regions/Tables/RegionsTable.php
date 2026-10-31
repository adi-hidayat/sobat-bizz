<?php

namespace App\Filament\Resources\Regions\Tables;

use App\Filament\Actions\TableActions;
use App\Filament\Resources\Regions\RegionData;
use App\Filament\Resources\Regions\Schemas\RegionForm;
use App\Models\Region;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class RegionsTable
{
    /**
     * @var array<string, string>
     */
    private const PARENT_LEVELS = [
        Region::LEVEL_CITY => Region::LEVEL_STATE,
        Region::LEVEL_DISTRICT => Region::LEVEL_CITY,
        Region::LEVEL_VILLAGE => Region::LEVEL_DISTRICT,
    ];

    public static function configure(Table $table, string $level): Table
    {
        $columns = [
            TextColumn::make('name')
                ->label(__('common.fields.name'))
                ->searchable()
                ->sortable(),
            TextColumn::make('code')
                ->label(__('common.fields.code'))
                ->searchable()
                ->sortable()
                ->toggleable(),
        ];

        if ($level === Region::LEVEL_STATE) {
            $columns[] = TextColumn::make('country.name')
                ->label(__('common.fields.country'))
                ->searchable()
                ->sortable();
        } elseif ($level !== Region::LEVEL_COUNTRY) {
            $columns[] = TextColumn::make('parent.name')
                ->label(RegionForm::parentLabel($level))
                ->searchable()
                ->sortable();
            $columns[] = TextColumn::make('country.name')
                ->label(__('common.fields.country'))
                ->toggleable();
        }

        if ($level === Region::LEVEL_VILLAGE) {
            $columns[] = TextColumn::make('postal_code')
                ->label(__('common.fields.postal_code'))
                ->searchable();
        } else {
            $columns[] = TextColumn::make('children_count')
                ->label(self::childLabel($level))
                ->counts('children');
        }

        $columns[] = TextColumn::make('updated_at')
            ->label(__('common.fields.updated_at'))
            ->dateTime('d M Y H:i')
            ->toggleable(isToggledHiddenByDefault: true);

        $filters = [];

        if ($level !== Region::LEVEL_COUNTRY) {
            $filters[] = SelectFilter::make('country_id')
                ->label(__('common.fields.country'))
                ->relationship(
                    'country',
                    'name',
                    modifyQueryUsing: fn (Builder $query): Builder => $query
                        ->where('level', Region::LEVEL_COUNTRY)
                        ->orderBy('name'),
                )
                ->searchable();
        }

        $parentLevel = self::PARENT_LEVELS[$level] ?? null;

        if ($parentLevel !== null) {
            $filters[] = SelectFilter::make('parent_id')
                ->label(RegionForm::parentLabel($level))
                ->relationship(
                    'parent',
                    'name',
                    modifyQueryUsing: fn (Builder $query): Builder => $query
                        ->where('level', $parentLevel)
                        ->orderBy('name'),
                )
                ->searchable();
        }

        return $table
            ->columns($columns)
            ->filters($filters)
            ->recordActions([
                TableActions::group([
                    ViewAction::make(),
                    EditAction::make()
                        ->modal()
                        ->slideOver()
                        ->modalWidth(Width::ThreeExtraLarge)
                        ->mutateDataUsing(fn (array $data): array => RegionData::prepare($data, $level)),
                ]),
            ])
            ->recordUrl(null)
            ->recordAction('view')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function childLabel(string $level): string
    {
        return match ($level) {
            Region::LEVEL_COUNTRY => __('common.region_levels.state'),
            Region::LEVEL_STATE => __('common.region_levels.city'),
            Region::LEVEL_CITY => __('common.region_levels.district'),
            default => __('common.region_levels.village'),
        };
    }
}
