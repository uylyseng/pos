<div class="overflow-x-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Order #{{ $order->id }}</h1>

            <div class="flex space-x-3">
                <a href="{{ route('orders.receipt', $order->id) }}" target="_blank"
                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Receipt
                </a>

                <a href="{{ route('orders.index') }}"
                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Orders
                </a>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Order Details</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Created on {{ $order->created_at->format('M d, Y h:i A') }}</p>
                </div>

                @if ($order->payments->where('status', 'completed')->count() > 0)
                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        Paid
                    </span>
                @elseif ($order->payments->where('status', 'pending')->count() > 0)
                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        Pending
                    </span>
                @elseif ($order->payments->where('status', 'failed')->count() > 0)
                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        Failed
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        Unpaid
                    </span>
                @endif
            </div>

            <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                <dl class="sm:divide-y sm:divide-gray-200">
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Cashier</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->createdBy->name ?? 'Unknown' }}</dd>
                    </div>

                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Subtotal</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">${{ number_format($order->subtotal, 2) }}</dd>
                    </div>

                    @if($order->discount_amount > 0)
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Discount</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            ${{ number_format($order->discount_amount, 2) }}
                            @if($order->discount)
                                ({{ $order->discount->name_en ?? 'Discount' }})
                            @endif
                        </dd>
                    </div>
                    @endif

                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Total</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 sm:mt-0 sm:col-span-2">
                            ${{ number_format($order->total, 2) }}
                            <span class="text-sm text-gray-500">
                                (៛{{ number_format(round($order->total * $order->exchange_rate)) }})
                            </span>
                        </dd>
                    </div>

                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @forelse($order->payments as $payment)
                                <div class="mb-1">
                                    {{ $payment->paymentMethod->name ?? 'Unknown Method' }}
                                    ({{ $payment->currency->code ?? 'USD' }})
                                    - {{ $payment->status }}
                                    @if($payment->currency_id == 1)
                                        ${{ number_format($payment->amount, 2) }}
                                    @else
                                         ៛{{ number_format($payment->amount) }}
                                    @endif
                                </div>
                            @empty
                                No payment recorded
                            @endforelse
                        </dd>
                    </div>

                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Exchange Rate</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            $1 = ៛{{ number_format($order->exchange_rate) }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Order Items</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ $order->items->count() }} items in this order</p>
            </div>

            <div class="border-t border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-normal">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $item->product->name_en ?? 'Unknown Product' }}
                                    </div>

                                    @if($item->product->name_km)
                                        <div class="text-sm text-gray-500">{{ $item->product->name_km }}</div>
                                    @endif

                                    @if($item->size)
                                        <div class="text-sm text-gray-500">Size: {{ $item->size->name_en }}</div>
                                    @endif

                                    @if($item->toppings->count() > 0)
                                        <div class="mt-1">
                                            <p class="text-xs font-medium text-gray-500">Toppings:</p>
                                            <ul class="text-xs text-gray-500">
                                                @foreach($item->toppings as $topping)
                                                    <li>
                                                        + {{ optional($topping->productTopping)->topping->name_en ?? 'Topping' }}
                                                        (${{ number_format($topping->price, 2) }})
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                    ${{ number_format($item->unit_price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    ${{ number_format($item->subtotal, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
