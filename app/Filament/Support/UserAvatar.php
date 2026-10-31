<?php

namespace App\Filament\Support;

use App\Models\User;

final class UserAvatar
{
    /**
     * Label opsi select dengan avatar user.
     */
    public static function optionLabel(User $user, ?string $label = null): string
    {
        return sprintf(
            '<span style="display:inline-flex;align-items:center;gap:0.5rem;height:1.25rem;line-height:1"><img src="%s" alt="" style="display:block;flex:none;width:1.25rem;height:1.25rem;border-radius:9999px;object-fit:cover" /><span>%s</span></span>',
            e($user->avatar_url),
            e($label ?? $user->name),
        );
    }
}
