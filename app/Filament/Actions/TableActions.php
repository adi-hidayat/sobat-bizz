<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Icons\Heroicon;

final class TableActions
{
    /**
     * Semua aksi baris dikumpulkan dalam satu menu tiga titik.
     *
     * @param  array<int, Action>  $actions
     */
    public static function group(array $actions): ActionGroup
    {
        return ActionGroup::make($actions)
            ->icon(Heroicon::EllipsisVertical)
            ->iconButton()
            ->color('gray')
            ->tooltip(__('common.actions.actions'));
    }

    /**
     * Tautan ke halaman detail penuh (untuk akses relation manager).
     */
    public static function detail(string|\Closure $url): Action
    {
        return Action::make('openDetail')
            ->label(__('common.actions.detail_page'))
            ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
            ->url($url);
    }
}
