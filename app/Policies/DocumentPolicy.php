<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Dokumen yang sudah terbit tidak boleh diubah atau dihapus langsung
 * (PRD 4.4, 4.5, 4.8) — perubahan harus lewat revisi/koreksi.
 */
abstract class DocumentPolicy extends ResourcePolicy
{
    public function update(User $user, Model $model): bool
    {
        return parent::update($user, $model)
            && method_exists($model, 'isEditable')
            && $model->isEditable();
    }

    public function delete(User $user, Model $model): bool
    {
        return parent::delete($user, $model)
            && method_exists($model, 'isEditable')
            && $model->isEditable();
    }

    /**
     * Penghapusan massal dimatikan agar dokumen terbit tidak bisa terhapus
     * tanpa melewati pengecekan status per dokumen.
     */
    public function deleteAny(User $user): bool
    {
        return false;
    }
}
