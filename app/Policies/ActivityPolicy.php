<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Activitylog\Models\Activity;

class ActivityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_activitylog');
    }


    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \Spatie\Activitylog\Models\Activity  $activity
     * @return bool
     */
    public function view(User $user, Activity $activity): bool
    {
        return $user->can('view_activitylog');
    }

}
