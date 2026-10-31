<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StaffPolicy extends ResourcePolicy
{
    protected function resource(): string
    {
        return 'staff';
    }

    public function delete(User $user, Model $model): bool
    {
        return $model->getAttribute('user_id') !== $user->getKey() && parent::delete($user, $model);
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
