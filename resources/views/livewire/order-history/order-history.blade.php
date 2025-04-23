<!-- filepath: /home/lyseng/Project/pos_/pos/resources/views/livewire/order-history/order-history.blade.php -->
<div class="overflow-x-auto">
    <div class="max-w-full lg:max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col lg:flex-row justify-between items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Orders Management</h1>
                <p class="text-sm text-gray-500 mt-1">View, manage and track all your orders</p>
            </div>

            <!-- Search Bar and Filter tabs -->
            <div class="w-full lg:w-auto flex flex-col sm:flex-row gap-3">
                <!-- Search input -->
                <div class="relative flex-grow max-w-md">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by order ID or customer..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500"
                    >
                    <div class="absolute right-0 top-0 mt-2 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <!-- Filter tabs -->
                <div class="flex space-x-1 bg-white rounded-lg shadow p-1">
                    <button wire:click="setStatus('all')" type="button"
                       class="px-4 py-2 rounded-md text-sm font-medium whitespace-nowrap {{ $status === 'all' ? 'bg-amber-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        All
                    </button>
                    <button wire:click="setStatus('pending')" type="button"
                       class="px-4 py-2 rounded-md text-sm font-medium whitespace-nowrap {{ $status === 'pending' ? 'bg-amber-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        Pending
                    </button>
                    <button wire:click="setStatus('completed')" type="button"
                       class="px-4 py-2 rounded-md text-sm font-medium whitespace-nowrap {{ $status === 'completed' ? 'bg-amber-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        Completed
                    </button>
                    <button wire:click="setStatus('failed')" type="button"
                       class="px-4 py-2 rounded-md text-sm font-medium whitespace-nowrap {{ $status === 'failed' ? 'bg-amber-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        Failed
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <!-- Order Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 border-b border-gray-200 hidden md:grid">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-blue-500 font-medium">Total Orders</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $ordersCount['total'] }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-sm text-green-500 font-medium">Completed</p>
                    <p class="text-2xl font-bold text-green-700">{{ $ordersCount['completed'] }}</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <p class="text-sm text-yellow-500 font-medium">Pending</p>
                    <p class="text-2xl font-bold text-yellow-700">{{ $ordersCount['pending'] }}</p>
                </div>
                <div class="bg-red-50 rounded-lg p-4">
                    <p class="text-sm text-red-500 font-medium">Failed</p>
                    <p class="text-2xl font-bold text-red-700">{{ $ordersCount['failed'] }}</p>
                </div>
            </div>

            <!-- Table Headers (Desktop only) -->
            <div class="hidden lg:flex bg-gray-50 px-6 py-3 text-sm font-medium text-gray-500 uppercase tracking-wider">
                <div class="w-1/12">Order ID</div>
                <div class="w-2/12">Date & Time</div>
                <div class="w-2/12">Items</div>
                <div class="w-2/12">Total</div>
                <div class="w-2/12">User</div>
                <div class="w-2/12">Status</div>
                <div class="w-1/12">Actions</div>
            </div>

            <ul role="list" class="divide-y divide-gray-200">
                @forelse ($orders as $order)
                    <li class="hover:bg-gray-50 transition-colors duration-150">
                        <!-- Mobile View -->
                        <div class="block lg:hidden">
                            <a href="{{ route('orders.show', $order->id) }}" class="block px-4 py-4">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-amber-600">
                                        Order #{{ $order->id }}
                                    </p>
                                    <div class="ml-2 flex-shrink-0">
                                        @if ($order->payments->where('status', 'completed')->count() > 0)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Paid
                                            </span>
                                        @elseif ($order->payments->where('status', 'pending')->count() > 0)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        @elseif ($order->payments->where('status', 'failed')->count() > 0)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Failed
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Unpaid
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2 grid grid-cols-2 gap-x-2 gap-y-1 text-sm">
                                    <div class="flex items-center text-gray-500">
                                        <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $order->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="flex items-center text-gray-500">
                                        <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $order->created_at->format('h:i A') }}
                                    </div>
                                    <div class="flex items-center text-gray-500">
                                        <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        {{ $order->items->count() }} items
                                    </div>
                                    <div class="flex items-center text-gray-500">
                                        <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        ${{ number_format($order->total, 2) }}
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Desktop View -->
                        <div class="hidden lg:flex px-6 py-3 items-center cursor-pointer">
                            <div class="w-1/12 font-medium text-amber-600">#{{ $order->id }}</div>
                            <div class="w-2/12">
                                <div class="font-medium">{{ $order->created_at->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $order->created_at->format('h:i A') }}</div>
                            </div>
                            <div class="w-2/12">
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    {{ $order->items->count() }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    @foreach($order->items->take(2) as $item)
                                        <span class="block truncate">{{ $item->product->name_en ?? 'Product' }}</span>
                                    @endforeach
                                    @if($order->items->count() > 2)
                                        <span class="block text-amber-600">+{{ $order->items->count() - 2 }} more</span>
                                    @endif
                                </div>
                            </div>
                            <div class="w-2/12 font-medium">${{ number_format($order->total, 2) }}</div>
                            <div class="w-2/12">
                                <div class="font-medium truncate">{{ $order->customer->name ?? 'Guest' }}</div>
                                <div class="text-xs text-gray-500">by {{ $order->createdBy->name ?? 'System' }}</div>
                            </div>
                            <div class="w-2/12">
                                @if ($order->payments->where('status', 'completed')->count() > 0)
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Paid
                                    </span>
                                @elseif ($order->payments->where('status', 'pending')->count() > 0)
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @elseif ($order->payments->where('status', 'failed')->count() > 0)
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Failed
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Unpaid
                                    </span>
                                @endif
                            </div>
                            <div class="w-1/12 flex space-x-2">
                                <a href="{{ route('orders.show', $order->id) }}" class="text-amber-600 hover:text-amber-900" title="View Order">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('orders.receipt', $order->id) }}" class="text-blue-600 hover:text-blue-900" title="Print Receipt" target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="py-10 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="mt-2 text-gray-500 text-sm">No orders found</p>
                        @if($search)
                            <p class="mt-1 text-gray-500 text-sm">Try using different search terms</p>
                        @endif
                    </li>
                @endforelse
            </ul>

            <!-- Pagination -->
            <div class="px-4 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ $orders->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $orders->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $orders->total() }}</span> results
                        </p>
                    </div>
                    <div>
                        {{ $orders->links() }}
                    </div>
                </div>

                <div class="flex sm:hidden justify-center w-full">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
