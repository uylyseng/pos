<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    x-data
    x-show="$wire.showModal"
    x-cloak
    x-on:keydown.escape.window="$wire.closeModal()">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Modal Header -->
        <div class="p-4 border-b flex justify-between items-center sticky top-0 bg-white z-10">
            <h3 class="font-bold text-xl">
                {{ $isPendingCheckout ? 'Pay Later Order' : 'Checkout' }}
            </h3>
            <button wire:click="closeModal" class="text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body with scrollable content -->
        <div class="overflow-y-auto p-4 flex flex-col md:flex-row gap-4">
            <!-- Order Summary -->
            <div class="md:w-3/5 bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-medium">Order Summary</h4>

                    <!-- Table Selection -->
                    <div class="flex items-center">
                        <label class="block text-sm mr-4">Table:</label>
                        <div class="relative">
                            <input
                                type="number"
                                wire:model.live="tableNumber"
                                min="1"
                                max="10"
                                placeholder="1-10"
                                class="rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500 w-20 {{ $isTableRequired && !$tableNumber ? 'border-red-300' : '' }}"
                            >
                            @error('table')
                                <div class="text-red-500 text-xs absolute">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="max-h-80 overflow-y-auto mb-4">
                    <table class="w-full">
                        <thead class="bg-white sticky top-0">
                            <tr class="border-b">
                                <th class="text-left py-2 w-16">Image</th>
                                <th class="text-left py-2">Product</th>
                                <th class="text-center py-2">Size</th>
                                <th class="text-center py-2">Qty</th>
                                <th class="text-right py-2">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $index => $item)
                                <tr class="border-b">
                                    <td class="py-2">
                                        @if(!empty($item['image_url']))
                                            <img src="{{ $item['image_url'] }}" alt="{{ $item['name_en'] }}" class="w-12 h-12 object-cover rounded">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-2">
                                        <span>{{ $item['name_km'] }}</span>
                                        <div class="text-xs text-gray-500">
                                            <span>{{ $item['name_en'] }}</span>
                                        </div>
                                        @if(!empty($item['toppings']) && count($item['toppings']) > 0)
                                            <div class="text-xs text-gray-500">
                                                @foreach($item['toppings'] as $i => $topping)
                                                    <span>{{ $topping['name_km'] }} ({{ $topping['name_en'] }}){{ $i < count($item['toppings']) - 1 ? ', ' : '' }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center py-2">
                                        @if(!empty($item['size']))
                                            <div>{{ $item['size']['name_km'] ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">({{ $item['size']['name_en'] ?? '-' }})</div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center py-2">{{ $item['quantity'] }}</td>
                                    <td class="text-right py-2">${{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="bg-white rounded p-4">
                    <div class="flex justify-between py-1">
                        <span>Subtotal:</span>
                        <div class="text-right">
                            <span>${{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>

                    <!-- Discount section -->
                    @if(!empty($discount))
                        <div class="flex justify-between py-1 text-green-600">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $discount['name_km'] }}</span>
                                <span class="text-xs ml-1 text-gray-500">({{ $discount['name_en'] }})</span>
                                <span class="text-xs ml-1">
                                    ({{ $discount['type'] === 'percentage' ? $discount['amount'] . '%' : '$' . $discount['amount'] }})
                                </span>
                            </span>
                            <div class="text-right">
                                <span>-${{ number_format($discountAmount, 2) }}</span>
                                <span class="block text-xs">-៛{{ number_format(round($discountAmount * $exchangeRate)) }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Exchange Rate Display -->
                    <div class="flex justify-between py-1 text-gray-600 border-t mt-1">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8l3 5m0 0l3-5m-3 5v4m-3-5h6m-6 3h6m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Exchange Rate:
                        </span>
                        <div class="text-right">
                            <span>$1 = ៛{{ number_format($exchangeRate) }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between pt-2 font-bold border-t mt-1">
                        <span>Total:</span>
                        <div class="text-right">
                            <span>${{ number_format($total, 2) }}</span>
                            <span class="block text-xs text-gray-500">៛{{ number_format(round($total * $exchangeRate)) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Options -->
            <div class="md:w-2/5 bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium mb-4">Payment Method</h4>

                <!-- Payment method selector -->
                <div class="grid grid-cols-3 gap-2 mb-4">
                    @foreach($paymentMethods as $method)
                        @php
                            $methodId = strtolower($method->name_en);
                            $colorMap = [
                                'cash' => 'green',
                                'card' => 'blue',
                                'mobile' => 'amber',
                                'bank transfer' => 'purple',
                                'qr' => 'red',
                            ];
                            $color = $colorMap[$methodId] ?? 'gray';

                            $iconMap = [
                                'cash' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                                'card' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                                'mobile' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
                                'bank transfer' => 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z',
                                'qr' => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z',
                            ];
                            $icon = $iconMap[$methodId] ?? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
                        @endphp
                        <label class="bg-white p-3 rounded-lg border-2 transition-all cursor-pointer flex flex-col items-center
                               {{ $paymentMethod === $methodId ? 'border-'.$color.'-600' : 'border-transparent' }}">
                            <input type="radio" name="paymentMethod" value="{{ $methodId }}" wire:model.live="paymentMethod" class="sr-only">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1 text-{{ $color }}-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                            </svg>
                            <span class="text-sm capitalize text-{{ $color }}-600">{{ $method->name_en }}</span>
                        </label>
                    @endforeach
                </div>

                <!-- Cash payment details -->
                @if($paymentMethod === 'cash')
                    <div class="mb-4">
                        <!-- Amount inputs -->
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <!-- USD Amount -->
                            <div>
                                <label class="block text-sm mb-1">USD Amount</label>
                                <div class="flex items-center">
                                    <span class="text-sm mr-2">$</span>
                                    <input type="number" wire:model.live="cashAmount" min="0" step="0.01"
                                        class="w-full rounded border-gray-300 focus:border-green-500 focus:ring-amber-500">
                                </div>
                            </div>

                            <!-- KHR Amount -->
                            <div >
                                <label class="block text-sm mb-1">KHR Amount</label>
                                <div class="flex items-center">
                                    <span class="text-sm mr-2">៛</span>
                                    <input type="number" wire:model.live="cashAmountRiel" min="0" step="100"
                                        class="w-full rounded border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                                </div>
                            </div>
                        </div>

                        <!-- Quick amount buttons -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Quick Amount</label>
                            <div class="grid grid-cols-3 gap-2 mb-2">
                                @foreach([ceil($total), ceil($total) + 5, ceil($total) + 10] as $amount)
                                    <button
                                        wire:click="setCashAmount({{ $amount }})"
                                        type="button"
                                        class="bg-white p-3 rounded-lg border border-gray-200 text-sm font-medium hover:bg-green-50 hover:border-green-600 transition-colors flex items-center justify-center"
                                    >
                                        <span class="text-gray-600 mr-1">$</span>{{ $amount }}
                                    </button>
                                @endforeach
                            </div>

                            <!-- KHR quick buttons -->
                            <label class="block text-sm font-medium mb-2">KHR Amount</label>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach([10000, 20000, 50000] as $amount)
                                    <button
                                        wire:click="setCashAmountRiel({{ $amount }})"
                                        type="button"
                                        class="bg-white p-3 rounded-lg border border-gray-200 text-sm font-medium hover:bg-green-50 hover:border-green-600 transition-colors flex items-center justify-center"
                                    >
                                        <span class="text-green-600 mr-1">{{ number_format($amount) }}៛</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Change display -->
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-sm mb-1">Change (USD)</label>
                                <div class="bg-white p-2 rounded border border-gray-200 font-medium">
                                    ${{ number_format($changeUSD, 2) }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm mb-1">Change (KHR)</label>
                                <div class="bg-white p-2 rounded border border-gray-200 font-medium">
                                     ៛{{ number_format(round($changeKHR)) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Card/Mobile payment placeholder -->
                    <div class="bg-white p-4 rounded-lg text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-gray-500">Ready to process payment</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 border-t bg-gray-50">
            <!-- User info -->
            @if($currentUser)
            <div class="mb-3 text-sm text-gray-500 flex justify-between items-center">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ $currentUser->name }}
                </span>

                <span>
                    @if($tableNumber)
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Table {{ $tableNumber }}
                    @endif
                </span>
            </div>
            @endif

            <!-- Add this in the footer section of your modal -->
            <div class="text-sm text-gray-500 flex justify-between mb-2">
                <span>Exchange Rate:</span>
                <span>1 USD = {{ number_format($exchangeRate) }} KHR</span>
            </div>

            <div class="flex space-x-2">
                <button
                    id="complete-order-btn"
                    wire:click="completeOrder"
                    type="button"
                    class="flex-1 bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition font-medium
                          {{ $canCompleteOrder && !$isPendingCheckout ? '' : 'opacity-50 cursor-not-allowed' }}"
                    wire:loading.attr="disabled"
                    wire:target="completeOrder"
                    {{ ($canCompleteOrder && !$isPendingCheckout) ? '' : 'disabled' }}
                    x-show="!$wire.isPendingCheckout"
                >
                    <div wire:loading wire:target="completeOrder">
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 inline-block text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </div>
                    <div wire:loading.remove wire:target="completeOrder">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Complete Order (${{ number_format($total, 2) }})
                    </div>
                </button>

                <button
                    id="pending-order-btn"
                    wire:click="markAsPending"
                    type="button"
                    class="flex-1 bg-amber-500 text-white py-3 px-4 rounded-lg hover:bg-amber-600 transition font-medium"
                    wire:loading.attr="disabled"
                    wire:target="markAsPending"
                >
                    <div wire:loading wire:target="markAsPending">
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 inline-block text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </div>
                    <div wire:loading.remove wire:target="markAsPending">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pay Later
                    </div>
                </button>
            </div>

            @error('order')
                <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
