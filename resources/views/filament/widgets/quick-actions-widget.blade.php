<x-filament::section>
    <div class="space-y-4">
        <h2 class="text-lg font-medium tracking-tight">Quick Actions</h2>

        <div class="grid grid-cols-2 gap-3">
            @can('create', \App\Models\Order::class)
                <x-filament::button
                    color="primary"
                    icon="heroicon-m-shopping-cart"
                    :href="url('/admin/resources/orders/create')"
                    tag="a"
                >
                    New Order
                </x-filament::button>
            @endcan

            @can('create', \App\Models\Product::class)
                <x-filament::button
                    color="success"
                    icon="heroicon-m-cube"
                    :href="url('/admin/resources/products/create')"
                    tag="a"
                >
                    Add Product
                </x-filament::button>
            @endcan

            @can('view-any', \App\Models\User::class)
                <x-filament::button
                    color="info"
                    icon="heroicon-m-users"
                    :href="url('/admin/resources/users')"
                    tag="a"
                >
                    Manage Users
                </x-filament::button>
            @endcan

            @if(auth()->user()->can('access_pos'))
                <x-filament::button
                    color="warning"
                    icon="heroicon-m-calculator"
                    href="/pos"
                    tag="a"
                >
                    POS System
                </x-filament::button>
            @endif
        </div>
    </div>
</x-filament::section>
