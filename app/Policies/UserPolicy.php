<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserPolicy extends ResourcePolicy
{
    protected function resource(): string
    {
        return 'user';
    }

    public function delete(User $user, Model $model): bool
    {
        return $model->getKey() !== $user->getKey() && parent::delete($user, $model);
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
