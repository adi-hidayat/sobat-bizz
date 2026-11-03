<?php

namespace App\Filament\Resources\Leads\RelationManagers;

use App\Enums\LeadActivityGroup;
use App\Filament\Actions\TableActions;
use App\Models\LeadActivity;
use App\Models\LeadActivityType;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $recordTitleAttribute = 'subject';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('resources/lead.relation_managers.activities.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('lead_activity_type_id')
                    ->label(__('resources/lead.fields.activity_type'))
                    ->options(fn (): array => LeadActivityType::groupedOptions())
                    ->default(function (): ?int {
                        $types = LeadActivityType::query()->active()->ordered()->get();

                        return ($types->firstWhere('key', 'call') ?? $types->first())?->getKey();
                    })
                    ->required()
                    ->searchable()
                    ->live(),
                TextInput::make('subject')
                    ->label(__('resources/lead.fields.activity_subject'))
                    ->maxLength(255),
                DateTimePicker::make('occurred_at')
                    ->label(__('resources/lead.fields.activity_occurred_at'))
                    ->default(now())
                    ->required(),
                DateTimePicker::make('due_at')
                    ->label(__('resources/lead.fields.activity_due_at'))
                    ->visible(fn (Get $get): bool => $this->isTask($get)),
                Textarea::make('notes')
                    ->label(__('common.fields.notes'))
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('subject')
            ->columns([
                TextColumn::make('activityType.name')
                    ->label(__('resources/lead.fields.activity_type'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('activityType.group')
                    ->label(__('resources/lead.fields.activity_group'))
                    ->badge()
                    ->formatStateUsing(fn (?LeadActivityGroup $state): string => $state?->getLabel() ?? '-'),
                TextColumn::make('subject')
                    ->label(__('resources/lead.fields.activity_subject'))
                    ->searchable()
                    ->wrap(),
                TextColumn::make('occurred_at')
                    ->label(__('resources/lead.fields.activity_occurred_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('due_at')
                    ->label(__('resources/lead.fields.activity_due_at'))
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->color(fn (LeadActivity $record): ?string => $record->isOverdue() ? 'danger' : null),
                TextColumn::make('user.name')
                    ->label(__('common.fields.owner'))
                    ->toggleable(),
                TextColumn::make('completed_at')
                    ->label(__('resources/lead.fields.activity_completed_at'))
                    ->dateTime('d M Y H:i')
                    ->placeholder(__('resources/lead.activity.pending'))
                    ->toggleable(),
            ])
            ->defaultSort('occurred_at', 'desc')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                TableActions::group([
                    Action::make('complete')
                        ->label(__('resources/lead.activity.mark_completed'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (LeadActivity $record): bool => $record->completed_at === null)
                        ->action(fn (LeadActivity $record): bool => $record->update(['completed_at' => now()])),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private function isTask(Get $get): bool
    {
        $typeId = $get('lead_activity_type_id');

        if (blank($typeId)) {
            return false;
        }

        return LeadActivityType::query()->whereKey($typeId)->value('group') === LeadActivityGroup::Task->value;
    }
}
