<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class DashboardActivity
{
    use HandlesAuthorization;

    /**
     * Register the dashboard activity policies.
     *
     * @return void
     */
    public static function register(): void
    {
        Gate::define('widget_AdminStatsOverview', function (User $user) {
            return $user->can('view_dashboard');
        });

        Gate::define('widget_LatestOrders', function (User $user) {
            return $user->can('view_dashboard');
        });

        Gate::define('widget_TopSellingProducts', function (User $user) {
            return $user->can('view_dashboard');
        });

        Gate::define('widget_CashierStateOverview', function (User $user) {
            return $user->can('view_dashboard');
        });
    }

    /**
     * Determine whether the user can view any dashboard widgets.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_dashboard');
    }
}
