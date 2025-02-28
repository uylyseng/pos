<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class POSPolicy
{
    use HandlesAuthorization;

    public function AccessPOS(User $user): bool
    {
        return $user->can('access_pos');
    }
}
