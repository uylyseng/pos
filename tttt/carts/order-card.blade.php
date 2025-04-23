<!-- Cart Items -->
<div class="flex-1 overflow-y-auto p-3">
    <div x-show="cart.length === 0" class="text-gray-500 text-center py-8">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        Your cart is empty
    </div>

    <div x-show="cart.length > 0" class="space-y-3">
        <template x-for="(item, index) in (Array.isArray(cart) ? cart : [])" :key="index">
            <div class="bg-gray-50 p-3 rounded-lg cursor-pointer border border-gray-300">
                <!-- Cart item with image -->
                <div class="flex items-start space-x-2">
                    <!-- Product image -->
                    <div class="h-12 w-12 rounded-md bg-gray-100 overflow-hidden flex-shrink-0">
                        <img :src="getImageUrl(item.image)" class="h-full w-full object-cover">
                    </div>

                    <!-- Product info -->
                    <div class="flex-1 min-w-0">
                        <div class="font-medium truncate">
                            <span x-text="item.name_km"></span>
                            <span class="text-xs text-gray-500 ml-1" x-text="item.name_en"></span>
                        </div>
                        <div class="text-xs text-amber-600 mt-0.5" x-text="'$' + item.price.toFixed(2)"></div>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex space-x-2">
                        <!-- Edit button -->
                        <button @click="editCartItem(index, $event)" class="text-gray-400 hover:text-blue-500 p-1.5 rounded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>

                        <!-- Remove button -->
                        <button @click="removeFromCart(index, $event)" class="text-gray-400 hover:text-red-500 p-1.5 rounded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Quantity controls and subtotal -->
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-200">
                    <div class="flex border rounded-md">
                        <button @click="decreaseQuantity(index)" class="w-7 h-7 flex items-center justify-center text-gray-600">-</button>
                        <div class="w-8 h-7 flex items-center justify-center bg-gray-50">
                            <span x-text="item.quantity"></span>
                        </div>
                        <button @click="increaseQuantity(index)" class="w-7 h-7 flex items-center justify-center text-gray-600">+</button>
                    </div>
                    <div class="font-semibold text-amber-600" x-text="'$' + (item.price * item.quantity).toFixed(2)"></div>
                </div>

                <!-- Show size if selected -->
                <div x-show="item.size" class="text-xs text-gray-600 mt-1">
                    Size:
                    <span x-text="item.size.name_km"></span>
                    <span class="text-xs text-gray-500 ml-1" x-text="item.size.name_en"></span>
                </div>

                <!-- Show toppings if any selected -->
                <div x-show="item.toppings && item.toppings.length > 0" class="text-xs text-gray-600 mt-1">
                    <span>Toppings:</span>
                    <ul class="pl-4 mt-0.5">
                        <template x-for="(topping, i) in item.toppings" :key="i">
                            <li x-text="topping.name_en + ' (+$' + topping.price + ')'"></li>
                        </template>
                    </ul>
                </div>

                <!-- Show special instructions if any -->
                <div x-show="item.special_instructions" class="text-xs text-gray-600 mt-1">
                    <span x-text="item.special_instructions"></span>
                </div>
            </div>
        </template>
    </div>
</div>
