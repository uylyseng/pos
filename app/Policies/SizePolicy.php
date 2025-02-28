<?php

namespace App\Policies;

use App\Models\Size;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SizePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_size');
    }

    public function view(User $user, Size $size): bool
    {
        return $user->can('view_size');
    }

    public function delete(User $user, Size $size): bool
    {
        return $user->can('delete_size');
    }

    public function create(User $user): bool
    {
        return $user->can('create_size');
    }

    public function update(User $user): bool
    {
        return $user->can('update_size');
    }

    public function restore(User $user, Size $size): bool
    {
        return $user->can('restore_size');
    }

    public function replicate(User $user, Size $size): bool
    {
        return $user->can('replicate_size');
    }

}
