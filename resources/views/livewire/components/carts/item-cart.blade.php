<div class="flex-1 overflow-y-auto p-3">
    @if($hasItems)
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-medium text-gray-700">Cart Items ({{ count($items) }})</h3>
            <button
                wire:click="clearCart"
                wire:loading.attr="disabled"
                wire:target="clearCart"
                class="text-xs text-red-600 hover:text-red-800 flex items-center transition-colors duration-200 hover:animate-shake group"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 transition-transform duration-300 group-hover:rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>Clear All</span>

                <svg wire:loading wire:target="clearCart" class="animate-spin ml-1 h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </div>
    @endif

    <!-- Empty cart state -->
    @if(!$hasItems)
        <div class="text-gray-500 text-center py-8 animate-fade-in">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-2 animate-bounce-slow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Your cart is empty
        </div>
    @else
        <!-- Cart items list -->
        <div class="space-y-3">
            @foreach($items as $index => $item)
                <div wire:key="cart-item-{{ $index }}"
                     class="bg-gray-50 p-3 rounded-lg cursor-pointer border border-green-300 transform transition duration-200 hover:scale-[1.01] hover:shadow-md animate-fade-in"
                     style="animation-delay: {{ $index * 0.05 }}s">
                    <!-- Cart item with image -->
                    <div class="flex items-start space-x-2">
                        <!-- Product image -->
                        <div class="h-12 w-12 rounded-md bg-gray-100 overflow-hidden flex-shrink-0 transition-all duration-300 hover:opacity-90">
                            @if(isset($item['image']) && $item['image'])
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

                        <!-- Rest of the content remains the same -->
                        <!-- Product info -->
                        <div class="flex-1 min-w-0">
                            <div class="font-medium truncate">
                                <span>{{ $item['name_km'] ?? '' }}</span>
                                <span class="text-xs text-gray-500 ml-1">{{ $item['name_en'] ?? '' }}</span>
                            </div>
                            <div class="text-xs text-green-600 mt-0.5">${{ number_format($item['price'], 2) }}</div>
                        </div>

                        <!-- Action buttons -->
                        <div class="flex space-x-2">
                            <!-- Edit button -->
                            <button
                                wire:click="editItem({{ $index }})"
                                wire:loading.attr="disabled"
                                wire:target="editItem({{ $index }})"
                                class="text-gray-400 hover:text-blue-500 p-1.5 rounded transition-colors duration-200">
                                <svg class="w-5 h-5 transform transition hover:rotate-12 duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>

                            <!-- Remove button -->
                            <button
                                wire:click="removeItem({{ $index }})"
                                wire:loading.attr="disabled"
                                wire:target="removeItem({{ $index }})"
                                class="text-gray-400 hover:text-red-500 p-1.5 rounded transition-colors duration-200">
                                <svg class="w-5 h-5 transform transition hover:rotate-90 duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Rest of the component code remains unchanged -->
                    <!-- Quantity controls and subtotal -->
                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-200">
                        <div class="flex border rounded-md overflow-hidden">
                            <button
                                wire:click="decreaseQuantity({{ $index }})"
                                wire:loading.attr="disabled"
                                wire:target="decreaseQuantity({{ $index }})"
                                class="w-7 h-7 flex items-center justify-center text-gray-600 hover:bg-red-100 focus:bg-red-200 transition-all duration-200 text-red-500"
                                @class(['opacity-50 cursor-not-allowed' => isset($item['quantity']) && $item['quantity'] <= 1])
                                @disabled(isset($item['quantity']) && $item['quantity'] <= 1)
                            >-</button>
                            <div class="w-8 h-7 flex items-center justify-center bg-gray-50">
                                <span class="transition-all duration-200">{{ $item['quantity'] }}</span>
                            </div>
                            <button
                                wire:click="increaseQuantity({{ $index }})"
                                wire:loading.attr="disabled"
                                wire:target="increaseQuantity({{ $index }})"
                                class="w-7 h-7 flex items-center justify-center text-gray-600 hover:bg-green-100 focus:bg-green-200 transition-all duration-200 text-green-500"
                            >+</button>
                        </div>
                        <div class="font-semibold text-black-600">${{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                    </div>

                    <!-- Show size if selected -->
                    @if(!empty($item['size']))
                        <div class="flex items-center text-xs text-gray-600 mt-1">
                            <span class="font-medium mr-1">Size:</span>
                            <span class="bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded-sm">
                                {{ $item['size']['name_km'] ?? '' }}
                                @if(!empty($item['size']['name_en']))
                                    <span class="text-blue-500 text-[10px]">({{ $item['size']['name_en'] }})</span>
                                @endif
                            </span>
                        </div>
                    @endif

                    <!-- Show toppings if any selected -->
                    @if(!empty($item['toppings']))
                        <div class="text-xs text-gray-600 mt-1">
                            <span class="font-medium">Toppings:</span>
                            <ul class="pl-4 mt-0.5">
                                @foreach($item['toppings'] as $topping)
                                    <li>{{ $topping['name_km'] ?? '' }} - {{ $topping['name_en'] ?? '' }} (+${{ number_format($topping['price'] ?? 0, 2) }})</li>
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
    @endif

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out forwards;
        }

        .animate-bounce-slow {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(-5%);
                animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
            }
            50% {
                transform: translateY(0);
                animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
            }
        }

        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-2px); }
            50% { transform: translateX(2px); }
            75% { transform: translateX(-2px); }
            100% { transform: translateX(0); }
        }

        .hover\:animate-shake:hover {
            animation: shake 0.4s ease-in-out;
        }
    </style>

{{-- <div class="flex items-center justify-center min-h-screen px-4">
    <div class="fixed inset-0 bg-black opacity-30" @click="show = false"></div>

    <div class="bg-white rounded-lg max-w-md w-full p-6 z-10 shadow-xl transform transition-all mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium" x-text="title"></h3>
            <button @click="show = false" class="text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="mb-6">
            <p class="text-gray-700" x-text="message"></p>
        </div>

        <div class="flex justify-end space-x-2">
            <button @click="show = false" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                Cancel
            </button>
            <button @click="onConfirm()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                Confirm
            </button>
        </div>
    </div>
</div> --}}


</div>



