<?php

namespace App\Policies;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentMethodPolicy
{

    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_payment::method');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->can('view_payment::method');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_payment::method');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->can('update_payment::method');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->can('delete_payment::method');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->can('restore_payment::method');
    }

    /**
    * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->can('replicate_payment::method');
    }

}
