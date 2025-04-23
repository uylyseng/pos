<?php

namespace App\Policies;

use App\Models\Topping;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ToppingPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_topping');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Topping $topping): bool
    {
        return $user->can('view_topping');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_topping');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Topping $topping): bool
    {
        return $user->can('update_topping');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Topping $topping): bool
    {
        return $user->can('delete_topping');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Topping $topping): bool
    {
        return $user->can('restore_topping');
    }


    public function replicate(User $user, Topping $topping): bool
    {
        return $user->can('replicate_topping');
    }

}
