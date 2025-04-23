<div class="flex-1 overflow-y-auto p-3">
    <!-- Empty cart state -->
    <div x-show="!$wire.hasItems" class="text-gray-500 text-center py-8">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        Your cart is empty
    </div>

    <!-- Cart items list -->
    <div x-show="$wire.hasItems" class="space-y-3">
        @foreach($items as $index => $item)
            <div wire:key="cart-item-{{ $index }}" class="bg-gray-50 p-3 rounded-lg cursor-pointer border border-gray-300">
                <!-- Cart item with image -->
                <div class="flex items-start space-x-2">
                    <!-- Product image -->
                    <div class="h-12 w-12 rounded-md bg-gray-100 overflow-hidden flex-shrink-0">
                        @if($item['image'])
                            <img src="{{ asset('storage/' . str_replace('public/', '', $item['image'])) }}"
                                 class="h-full w-full object-cover"
                                 alt="{{ $item['name_en'] ?? '' }}">
                        @else
                            <div class="h-full w-full flex items-center justify-center bg-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Product info -->
                    <div class="flex-1 min-w-0">
                        <div class="font-medium truncate">
                            <span>{{ $item['name_km'] ?? '' }}</span>
                            <span class="text-xs text-gray-500 ml-1">{{ $item['name_en'] ?? '' }}</span>
                        </div>
                        <div class="text-xs text-amber-600 mt-0.5">${{ number_format($item['price'], 2) }}</div>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex space-x-2">
                        <!-- Edit button -->
                        <button wire:click="editItem({{ $index }})" class="text-gray-400 hover:text-blue-500 p-1.5 rounded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>

                        <!-- Remove button -->
                        <button wire:click="removeItem({{ $index }})" class="text-gray-400 hover:text-red-500 p-1.5 rounded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Quantity controls and subtotal -->
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-200">
                    <div class="flex border rounded-md">
                        <button wire:click="decreaseQuantity({{ $index }})" class="w-7 h-7 flex items-center justify-center text-gray-600">-</button>
                        <div class="w-8 h-7 flex items-center justify-center bg-gray-50">
                            <span>{{ $item['quantity'] }}</span>
                        </div>
                        <button wire:click="increaseQuantity({{ $index }})" class="w-7 h-7 flex items-center justify-center text-gray-600">+</button>
                    </div>
                    <div class="font-semibold text-amber-600">${{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                </div>

                <!-- Show size if selected -->
                @if(!empty($item['size']))
                    <div class="text-xs text-gray-600 mt-1">
                        Size:
                        <span>{{ $item['size']['name_km'] ?? '' }}</span>
                        <span class="text-xs text-gray-500 ml-1">{{ $item['size']['name_en'] ?? '' }}</span>
                    </div>
                @endif

                <!-- Show toppings if any selected -->
                @if(!empty($item['toppings']))
                    <div class="text-xs text-gray-600 mt-1">
                        <span>Toppings:</span>
                        <ul class="pl-4 mt-0.5">
                            @foreach($item['toppings'] as $topping)
                                <li>{{ $topping['name_en'] ?? '' }} (+${{ number_format($topping['price'] ?? 0, 2) }})</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Show special instructions if any -->
                @if(!empty($item['special_instructions']))
                    <div class="text-xs text-gray-600 mt-1">
                        <span>{{ $item['special_instructions'] }}</span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
