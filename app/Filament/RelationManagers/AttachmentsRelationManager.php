<?php

namespace App\Filament\RelationManagers;

use App\Filament\Actions\TableActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('common.attachments.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('path')
                    ->label(__('common.attachments.file'))
                    ->disk(config('crm.attachments.disk'))
                    ->directory(config('crm.attachments.directory'))
                    ->storeFileNamesIn('original_name')
                    ->maxSize((int) config('crm.attachments.max_size_kb'))
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->required(),
                TextInput::make('original_name')
                    ->label(__('common.attachments.file_name'))
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('original_name')
                    ->label(__('common.attachments.file'))
                    ->searchable(),
                TextColumn::make('size_for_humans')
                    ->label(__('common.attachments.size')),
                TextColumn::make('uploadedBy.name')
                    ->label(__('common.attachments.uploaded_by'))
                    ->placeholder(__('common.placeholders.system')),
                TextColumn::make('created_at')
                    ->label(__('common.fields.date'))
                    ->dateTime('d M Y H:i'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('common.attachments.upload')),
            ])
            ->recordActions([
                TableActions::group([
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
