<?php

namespace App\Filament\Resources\Leads\Actions;

use App\Filament\Resources\Leads\LeadResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;

final class LeadViewModeActions
{
    public static function make(string $currentMode): ActionGroup
    {
        return ActionGroup::make([
            Action::make('tableMode')
                ->label(__('resources/lead.actions.table'))
                ->hiddenLabel()
                ->tooltip(__('resources/lead.actions.table'))
                ->icon('heroicon-o-list-bullet')
                ->color($currentMode === 'table' ? 'primary' : 'gray')
                ->url(LeadResource::getUrl('index', ['view' => 'table'])),
            Action::make('pipelineMode')
                ->label(__('resources/lead.actions.pipeline'))
                ->hiddenLabel()
                ->tooltip(__('resources/lead.actions.pipeline'))
                ->icon('heroicon-o-squares-2x2')
                ->color($currentMode === 'board' ? 'primary' : 'gray')
                ->url(LeadResource::getUrl('board')),
        ])->buttonGroup();
    }
}
