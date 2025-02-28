<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Discount;
use App\Models\PaymentMethod;
use App\Models\ExchangeRate;
use App\Models\Size;
use App\Models\Topping;
use App\Policies\ExchangeRatePolicy;
use App\Policies\ProductPolicy;
use App\Policies\PaymentMethodPolicy;
use App\Policies\CurrencyPolicy;
use App\Policies\DiscountPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\RolePolicy;
use App\Policies\SizePolicy;
use App\Policies\UserPolicy;
use App\Policies\ToppingPolicy;
use Illuminate\Support\ServiceProvider;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use App\Policies\ActivityPolicy;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;
use Livewire\Livewire;
use App\Livewire\Components\Modals\CheckoutModal;
use App\Livewire\Components\Carts\ItemCart;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Activity::class, ActivityPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(PaymentMethod::class, PaymentMethodPolicy::class);
        Gate::policy(ExchangeRate::class, ExchangeRatePolicy::class);
        Gate::policy(Currency::class, CurrencyPolicy::class);
        Gate::policy(Discount::class, DiscountPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Size::class, SizePolicy::class);
        Gate::policy(Topping::class, ToppingPolicy::class);
        Gate::policy(\Spatie\Activitylog\Models\Activity::class, \App\Policies\ActivityPolicy::class);
    }

    public function boot(): void
    {
        // Existing LanguageSwitch code
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en','km'])
                ->flags([
                    'km' => asset('flags/Cambodia.svg'),
                    'en' => asset('flags/USA.svg'),
                ])
                ->flagsOnly();
        });

    }
}
