<x-pos-layout>
    <div class="h-full"
         x-data="posApp()"
         x-cloak
         wire-ignore
         x-init="$nextTick(() => { try { init() } catch(e) { console.error('Initialization error:', e) } })">

    @if(auth()->user()->can('access_pos'))
        <!-- POS interface content -->
        <div class="hidden md:flex h-full">
            <!-- Categories -->
            <div class="w-1/6 bg-white border-r flex flex-col h-full">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold">Categories</h2>
                </div>
                <div class="flex-1 overflow-y-auto p-3 space-y-2">
                    <button
                        @click="setCategory(null)"
                        class="w-full text-left px-4 py-3 rounded-lg transition"
                        :class="selectedCategory === null ? 'bg-amber-600 text-white' : 'bg-gray-100 hover:bg-gray-200'"
                    >
                        All Products
                    </button>
                    @foreach ($categories as $category)
                        <button
                            @click="setCategory({{ $category->id }})"
                            class="w-full text-left px-4 py-3 rounded-lg transition"
                            :class="selectedCategory === {{ $category->id }} ? 'bg-amber-600 text-white' : 'bg-gray-100 hover:bg-gray-200'"
                        >
                            <div class="truncate">{{ $category->name_km }}</div>
                            <div class="text-xs truncate opacity-75">{{ $category->name_en }}</div>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Products -->
            <div class="w-3/5 flex flex-col h-full bg-gray-100">
                <div class="p-4 bg-white border-b flex justify-between items-center">
                    <h2 class="text-lg font-semibold">Products</h2>
                    <div class="relative">
                        <input
                            type="text"
                            placeholder="Search products..."
                            class="rounded-lg border-gray-300 pl-8 focus:ring-amber-500 focus:border-amber-500"
                            x-model="searchTerm"
                            @input="searchProducts"
                        >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-2 top-2.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Loading Spinner -->
                <div x-show="isLoading" class="flex-1 flex justify-center items-center">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-amber-600"></div>
                </div>

                <!-- Products Grid -->
                <div x-show="!isLoading" class="flex-1 overflow-y-auto p-4">
                    <div x-show="products.length === 0" class="flex h-full items-center justify-center">
                        <div class="text-center text-gray-500 bg-white p-8 rounded-lg shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                            </svg>
                            <p class="text-lg font-medium">No products found</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4" x-show="products.length > 0">
                        <template x-for="product in products" :key="product.id">
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden cursor-pointer hover:shadow-md transition" @click="openProductCustomization(product)">
                                <div class="aspect-w-1 aspect-h-1">
                                    <img :src="getImageUrl(product.image)" class="object-cover w-full h-40">
                                </div>
                                <div class="p-4">
                                    <div class="font-medium truncate" x-text="product.name_km"></div>
                                    <div class="text-sm text-gray-500 truncate" x-text="product.name_en"></div>
                                    <div class="flex justify-between items-center mt-2">
                                        <span class="text-amber-600 font-bold text-lg" x-text="'$' + product.base_price"></span>
                                        <button class="bg-amber-100 text-amber-600 p-1 rounded-full hover:bg-amber-200" @click.stop="openProductCustomization(product)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </button>
                                    </div>
                                    <!-- Indicators for size and topping availability -->
                                    <div class="flex space-x-2 mt-2">
                                        <span x-show="product.has_sizes" class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full">Sizes</span>
                                        <span x-show="product.has_toppings" class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full">Toppings</span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Cart -->
            <div class="w-1/3 bg-white border-l flex flex-col h-full">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold">Current Order</h2>
                </div>

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
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <!-- Cart item with image -->
                                <div class="flex items-start space-x-2">
                                    <!-- Product image -->
                                    <div class="h-12 w-12 rounded-md bg-gray-100 overflow-hidden flex-shrink-0">
                                        <img :src="getImageUrl(item.image)" class="h-full w-full object-cover">
                                    </div>

                                    <!-- Product info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium truncate" x-text="item.name_km"></div>
                                        <div class="text-xs text-gray-500 truncate" x-text="item.name_en"></div>
                                        <div class="text-xs text-amber-600 mt-0.5" x-text="'$' + item.price.toFixed(2)"></div>
                                    </div>

                                    <!-- Remove button -->
                                    <button @click="removeFromCart(index, $event)" class="text-gray-400 hover:text-red-500 ml-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
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
                                    Size: <span x-text="item.size.name_en"></span>
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
                                <div x-show="item.special_instructions" class="text-xs text-gray-600 italic mt-1">
                                    <span x-text="item.special_instructions"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Cart Footer - Desktop -->
                <div class="border-t p-4">
                    <div x-show="cart.length > 0">
                        <div class="flex justify-between items-center mb-2 text-sm text-gray-600">
                            <span>Subtotal:</span>
                            <span x-text="'$' + total.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-center mb-4 text-lg font-bold">
                            <span>Total:</span>
                            <span x-text="'$' + total.toFixed(2)"></span>
                        </div>
                    </div>

                    <button
                        @click="proceedToCheckout"
                        class="w-full bg-amber-600 text-white py-3 px-4 rounded-lg hover:bg-amber-700 transition font-medium"
                        :disabled="cart.length === 0"
                        :class="{'opacity-50 cursor-not-allowed': cart.length === 0}"
                    >
                        <span x-show="cart.length > 0">Checkout</span>
                        <span x-show="cart.length === 0">No Items</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Product Customization Modal -->
        <div x-show="showCustomizationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-cloak>
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col" @click.away="showCustomizationModal = false">
                <!-- Modal Header -->
                <div class="p-4 border-b flex justify-between items-center sticky top-0 bg-white z-10">
                    <h3 class="font-bold text-lg" x-text="selectedProduct ? selectedProduct.name_km : ''"></h3>
                    <button @click="showCustomizationModal = false" class="text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body with Scrollable Content -->
                <div class="flex-1 overflow-y-auto p-4">
                    <!-- Product Image and Base Info -->
                    <div class="flex items-center mb-6">
                        <div class="h-20 w-20 rounded-lg bg-gray-100 overflow-hidden mr-4">
                            <img :src="getImageUrl(selectedProduct?.image)" class="h-full w-full object-cover">
                        </div>
                        <div>
                            <h4 class="text-sm text-gray-500" x-text="selectedProduct ? selectedProduct.name_en : ''"></h4>
                            <div class="text-amber-600 font-bold mt-1" x-text="selectedProduct ? '$' + computeCurrentPrice() : ''"></div>
                        </div>
                    </div>

                    <!-- Size Selection -->
                    <div x-show="selectedProduct && selectedProduct.has_sizes && productSizes.length > 0" class="mb-6">
                        <h4 class="font-medium mb-2">Select Size</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="(size, index) in productSizes" :key="index">
                                <button
                                    class="border rounded-lg p-3 flex justify-between items-center transition-all"
                                    :class="selectedSizeIndex === index ? 'border-amber-600 bg-amber-50' : 'border-gray-200 hover:border-gray-300'"
                                    @click="selectSize(index)"
                                >
                                    <div>
                                        <div class="font-medium" x-text="size.size_name_km"></div>
                                        <div class="text-xs text-gray-500" x-text="size.size_name_en"></div>
                                    </div>
                                    <div class="text-amber-600" x-text="'$' + (selectedProduct.base_price * size.multiplier).toFixed(2)"></div>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Toppings Selection -->
                    <div x-show="selectedProduct && selectedProduct.has_toppings && productToppings.length > 0" class="mb-6">
                        <h4 class="font-medium mb-2">Add Toppings <span class="text-xs text-gray-500">(Select multiple)</span></h4>
                        <template x-for="(topping, index) in productToppings" :key="index">
                            <div class="border-b border-gray-100 py-2 last:border-b-0">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                class="form-checkbox h-5 w-5 text-amber-600 rounded"
                                                :checked="selectedToppings && selectedToppings[index]"
                                                @click="toggleTopping(index)"
                                            >
                                            <span class="ml-2" x-text="topping.topping_name_km"></span>
                                            <span class="text-xs text-gray-500 ml-2" x-text="topping.topping_name_en"></span>
                                        </label>
                                    </div>
                                    <div class="text-amber-600" x-text="'+ $' + topping.price"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Quantity Control -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-2">Quantity</h4>
                        <div class="flex items-center border rounded-md w-min">
                            <button @click="customQuantity > 1 ? customQuantity-- : null" class="px-4 py-2 text-lg">-</button>
                            <div class="w-12 text-center py-2 bg-gray-50 font-medium" x-text="customQuantity"></div>
                            <button @click="customQuantity++" class="px-4 py-2 text-lg">+</button>
                        </div>
                    </div>

                    <!-- Special Instructions (optional) -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-2">Special Instructions (optional)</h4>
                        <textarea
                            x-model="specialInstructions"
                            class="w-full border-gray-300 rounded-lg focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50"
                            rows="2"
                            placeholder="Any special requests..."
                        ></textarea>
                    </div>

                    <!-- Price Summary -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between mb-2">
                            <span>Base Price:</span>
                            <span x-text="selectedProduct ? '$' + getBasePrice() : '$0.00'"></span>
                        </div>
                        <div x-show="selectedToppingsCost > 0" class="flex justify-between mb-2">
                            <span>Toppings:</span>
                            <span x-text="'$' + selectedToppingsCost.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between font-bold">
                            <span>Item Total:</span>
                            <span x-text="'$' + (computeCurrentPrice() * customQuantity).toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer with Actions -->
                <div class="p-4 border-t bg-gray-50">
                    <button
                        @click="addCustomizedProductToCart"
                        class="w-full bg-amber-600 text-white py-3 px-4 rounded-lg hover:bg-amber-700 transition font-medium"
                    >
                        Add to Order
                    </button>
                </div>
            </div>
        </div>

        <!-- Checkout Modal -->
        <div x-show="showCheckoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-cloak>
            <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col">
                <!-- Modal Header -->
                <div class="p-4 border-b flex justify-between items-center sticky top-0 bg-white z-10">
                    <h3 class="font-bold text-xl">Checkout</h3>
                    <button @click="showCheckoutModal = false" class="text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body with scrollable content -->
                <div class="overflow-y-auto p-4 flex flex-col md:flex-row gap-4">
                    <!-- Order Summary -->
                    <div class="md:w-3/5 bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium mb-4">Order Summary</h4>

                        <!-- Order Items -->
                        <div class="max-h-80 overflow-y-auto mb-4">
                            <table class="w-full">
                                <thead class="bg-white sticky top-0">
                                    <tr class="border-b">
                                        <th class="text-left py-2">Product</th>
                                        <th class="text-center py-2">Size</th>
                                        <th class="text-center py-2">Qty</th>
                                        <th class="text-right py-2">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, index) in cart" :key="index">
                                        <tr class="border-b">
                                            <td class="py-2">
                                                <span x-text="item.name_km"></span>
                                                <!-- Add toppings if present -->
                                                <template x-if="item.toppings && item.toppings.length > 0">
                                                    <div class="text-xs text-gray-500">
                                                        <template x-for="(topping, i) in item.toppings" :key="i">
                                                            <span x-text="topping.name_km + (i < item.toppings.length - 1 ? ', ' : '')"></span>
                                                        </template>
                                                    </div>
                                                </template>
                                            </td>
                                            <td class="text-center py-2" x-text="item.size ? item.size.name_km : '-'"></td>
                                            <td class="text-center py-2" x-text="item.quantity"></td>
                                            <td class="text-right py-2" x-text="'$' + (item.price * item.quantity).toFixed(2)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Totals -->
                        <div class="bg-white rounded p-4">
                            <div class="flex justify-between py-1">
                                <span>Subtotal:</span>
                                <div class="text-right">
                                    <span x-text="'$' + subtotal.toFixed(2)"></span>
                                    <span class="block text-xs text-gray-500" x-text="'៛' + Math.round(subtotal * exchangeRate).toLocaleString()"></span>
                                </div>
                            </div>

                            <!-- Discount section -->
                            <template x-if="discount">
                                <div class="flex justify-between py-1 text-green-600">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span x-text="discount.name_km"></span>
                                        <span class="text-xs ml-1 text-gray-500" x-text="'(' + discount.name_en + ')'"></span>
                                        <span class="text-xs ml-1" x-text="'(' + (discount.type === 'percentage' ? discount.amount + '%' : '$' + discount.amount) + ')'"></span>
                                    </span>
                                    <div class="text-right">
                                        <span x-text="'-$' + discountAmount.toFixed(2)"></span>
                                        <span class="block text-xs" x-text="'-៛' + Math.round(discountAmount * exchangeRate).toLocaleString()"></span>
                                    </div>
                                </div>
                            </template>

                            <div class="flex justify-between pt-2 font-bold border-t mt-1">
                                <span>Total:</span>
                                <div class="text-right">
                                    <span x-text="'$' + total.toFixed(2)"></span>
                                    <span class="block text-xs text-gray-500" x-text="'៛' + Math.round(total * exchangeRate).toLocaleString()"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Options -->
                    <div class="md:w-2/5 bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium mb-4">Payment Method</h4>

                        <!-- Payment method selector -->
                        <div class="grid grid-cols-3 gap-2 mb-4">
                            <label class="bg-white p-3 rounded-lg border-2 transition-all cursor-pointer flex flex-col items-center"
                                   :class="paymentMethod === 'cash' ? 'border-amber-500' : 'border-transparent'">
                                <input type="radio" name="paymentMethodModal" value="cash" x-model="paymentMethod" class="sr-only">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="text-sm">Cash</span>
                            </label>

                            <label class="bg-white p-3 rounded-lg border-2 transition-all cursor-pointer flex flex-col items-center"
                                   :class="paymentMethod === 'card' ? 'border-amber-500' : 'border-transparent'">
                                <input type="radio" name="paymentMethodModal" value="card" x-model="paymentMethod" class="sr-only">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <span class="text-sm">Card</span>
                            </label>

                            <label class="bg-white p-3 rounded-lg border-2 transition-all cursor-pointer flex flex-col items-center"
                                   :class="paymentMethod === 'mobile' ? 'border-amber-500' : 'border-transparent'">
                                <input type="radio" name="paymentMethodModal" value="mobile" x-model="paymentMethod" class="sr-only">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span class="text-sm">Mobile</span>
                            </label>
                        </div>

                        <!-- Cash payment details -->
                        <div x-show="paymentMethod === 'cash'" class="mb-4">
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-sm mb-1">USD Amount</label>
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">$</span>
                                        <input type="number" x-model="cashAmount" min="0" step="0.01" class="w-full rounded border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm mb-1">KHR Amount</label>
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">៛</span>
                                        <input type="number" x-model="cashAmountRiel" min="0" step="100" class="w-full rounded border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Change display -->
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-sm mb-1">Change (USD)</label>
                                    <div class="bg-white p-2 rounded border border-gray-200 font-medium" x-text="'$' + changeUSD.toFixed(2)"></div>
                                </div>
                                <div>
                                    <label class="block text-sm mb-1">Change (KHR)</label>
                                    <div class="bg-white p-2 rounded border border-gray-200 font-medium" x-text="'៛' + Math.round(changeKHR).toLocaleString()"></div>
                                </div>
                            </div>

                            <!-- Quick amount selectors with improved design -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Quick Amount</label>
                                <div class="grid grid-cols-3 gap-2 mb-2">
                                    <button
                                        @click="setCashAmount(Math.ceil(total))"
                                        class="bg-white p-3 rounded-lg border border-gray-200 text-sm font-medium hover:bg-amber-50 hover:border-amber-300 transition-colors flex items-center justify-center"
                                    >
                                        <span class="text-amber-600 mr-1">$</span><span x-text="Math.ceil(total)"></span>
                                    </button>
                                    <button
                                        @click="setCashAmount(Math.ceil(total) + 5)"
                                        class="bg-white p-3 rounded-lg border border-gray-200 text-sm font-medium hover:bg-amber-50 hover:border-amber-300 transition-colors flex items-center justify-center"
                                    >
                                        <span class="text-amber-600 mr-1">$</span><span x-text="Math.ceil(total) + 5"></span>
                                    </button>
                                    <button
                                        @click="setCashAmount(Math.ceil(total) + 10)"
                                        class="bg-white p-3 rounded-lg border border-gray-200 text-sm font-medium hover:bg-amber-50 hover:border-amber-300 transition-colors flex items-center justify-center"
                                    >
                                        <span class="text-amber-600 mr-1">$</span><span x-text="Math.ceil(total) + 10"></span>
                                    </button>
                                </div>

                                <!-- Improved KHR quick buttons -->
                                <label class="block text-sm font-medium mb-2">KHR Amount</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button
                                        @click="setCashAmountRiel(10000)"
                                        class="bg-white p-3 rounded-lg border border-gray-200 text-sm font-medium hover:bg-amber-50 hover:border-amber-300 transition-colors flex items-center justify-center"
                                    >
                                        <span class="text-amber-600 mr-1">៛</span>10,000
                                    </button>
                                    <button
                                        @click="setCashAmountRiel(20000)"
                                        class="bg-white p-3 rounded-lg border border-gray-200 text-sm font-medium hover:bg-amber-50 hover:border-amber-300 transition-colors flex items-center justify-center"
                                    >
                                        <span class="text-amber-600 mr-1">៛</span>20,000
                                    </button>
                                    <button
                                        @click="setCashAmountRiel(50000)"
                                        class="bg-white p-3 rounded-lg border border-gray-200 text-sm font-medium hover:bg-amber-50 hover:border-amber-300 transition-colors flex items-center justify-center"
                                    >
                                        <span class="text-amber-600 mr-1">៛</span>50,000
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Card/Mobile payment placeholder -->
                        <div x-show="paymentMethod !== 'cash'" class="bg-white p-4 rounded-lg text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-gray-500">Ready to process payment</p>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t bg-gray-50">
                    <button
                        id="complete-order-btn"
                        @click="completeOrder"
                        class="w-full bg-amber-600 text-white py-3 px-4 rounded-lg hover:bg-amber-700 transition font-medium"
                        :disabled="!canCompleteOrder || isProcessingOrder"
                        :class="{'opacity-50 cursor-not-allowed': !canCompleteOrder || isProcessingOrder}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Complete Order (<span x-text="'$' + total.toFixed(2)"></span>)
                    </button>
                </div>
            </div>
        </div>

        <!-- Order Completed Modal -->
        <div x-show="orderCompleted" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 p-4" x-cloak>
            <div class="bg-white p-6 rounded-lg max-w-md w-full shadow-xl">
                <div class="text-center mb-6">
                    <div class="mx-auto rounded-full bg-green-100 p-3 h-24 w-24 flex items-center justify-center">
                        <svg class="h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold mt-4">Order Complete!</h2>
                    <p class="text-gray-600 mt-2">Your order has been processed successfully.</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Order Total:</span>
                        <span class="font-bold" x-text="'$' + total.toFixed(2) + ' / ៛' + Math.round(total * exchangeRate).toLocaleString()"></span>
                    </div>
                    <div class="flex justify-between py-2" x-show="paymentMethod === 'cash'">
                        <span class="text-gray-600">Amount Received:</span>
                        <span x-text="'$' + parseFloat(cashAmount).toFixed(2) + (cashAmountRiel > 0 ? ' + ៛' + parseInt(cashAmountRiel).toLocaleString() : '')"></span>
                    </div>
                    <div class="flex justify-between py-2 border-t" x-show="paymentMethod === 'cash'">
                        <span class="text-gray-600">Change:</span>
                        <div>
                            <span x-text="'$' + changeUSD.toFixed(2)"></span>
                            <span class="block text-sm text-gray-500" x-text="'៛' + Math.round(changeKHR).toLocaleString()"></span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <button @click="printReceipt" class="py-3 px-4 bg-gray-200 rounded-lg hover:bg-gray-300 transition flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Receipt
                    </button>
                    <button @click="resetApp" class="py-3 px-4 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        New Order
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="flex items-center justify-center h-screen">
            <div class="text-center p-6 bg-white rounded-lg shadow-lg">
                <h1 class="text-2xl font-bold text-red-600 mb-4">Access Denied</h1>
                <p class="mb-4">You do not have permission to access the POS system.</p>
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Back to Dashboard
                </a>
            </div>
        </div>
    @endif
    </div>

    <script>
        function posApp() {
            return {
                // Existing properties
                cart: [],
                selectedCategory: null,
                products: [],
                isLoading: true,
                mobileTab: 'products',
                searchTerm: '',
                allProducts: [],
                searchTimeout: null,
                currentFetch: null,
                searchFetch: null,

                // Customization properties
                showCustomizationModal: false,
                selectedProduct: null,
                productSizes: [],
                productToppings: [],
                selectedSizeIndex: 0,
                selectedToppings: [],
                customQuantity: 1,
                specialInstructions: '',

                // Add checkout-specific properties
                showCheckoutModal: false,
                orderCompleted: false,
                paymentMethod: 'cash',
                cashAmount: 0,
                cashAmountRiel: 0,
                exchangeRate: 4100,
                discount: null,
                discountAmount: 0,

                // Add this at the beginning of the posApp object:
                isProcessingOrder: false,

                init() {

                    this.cart = [];

                    // Load existing cart with stronger validation
                    try {
                        const savedCart = localStorage.getItem('pos_cart');
                        if (savedCart && savedCart !== 'undefined' && savedCart !== 'null') {
                            const parsed = JSON.parse(savedCart);
                            // Only assign if it's a valid array
                            if (Array.isArray(parsed)) {
                                this.cart = parsed;
                            }
                        }
                    } catch (error) {
                        console.error('Error loading cart from localStorage:', error);
                        // Reset to empty array, don't leave it undefined
                        this.cart = [];
                        localStorage.removeItem('pos_cart');
                    }

                    // Watch changes and update localStorage
                    this.$watch('cart', value => {
                        if (value && Array.isArray(value)) {
                            try {
                                localStorage.setItem('pos_cart', JSON.stringify(value));
                            } catch (error) {
                                console.error('Error saving cart to localStorage:', error);
                            }
                        }
                    });

                    this.loadProducts();

                    // Set appropriate tab based on screen size
                    if (window.innerWidth < 768 && this.cart.length > 0) {
                        this.mobileTab = 'cart';
                    }
                },

                getImageUrl(imagePath) {
                    if (!imagePath) return '/storage/products/default.jpg';

                    // Handle potential double paths like 'public/public/...'
                    return '/storage/' + imagePath.replace(/^public\//, '').replace(/^\//, '');
                },

                setCategory(categoryId) {
                    this.selectedCategory = categoryId;
                    this.searchTerm = ''; // Reset search when changing category
                    this.loadProducts();
                },

                loadProducts() {
                    // Show loading indicator immediately
                    this.isLoading = true;

                    // Cancel any existing fetch operation
                    if (this.currentFetch && typeof this.currentFetch.abort === 'function') {
                        this.currentFetch.abort();
                    }

                    // Create an AbortController to allow canceling this request if needed
                    const controller = new AbortController();
                    this.currentFetch = controller;

                    // Prevent cache issues with a timestamp
                    const timestamp = Date.now();
                    const url = `/pos/products?category_id=${this.selectedCategory || ''}&_=${timestamp}`;

                    fetch(url, {
                        signal: controller.signal,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        this.products = data || [];

                        // Store all products if it's the initial load or "All" category
                        if (this.selectedCategory === null) {
                            this.allProducts = [...(data || [])];
                        }

                        this.isLoading = false;
                    })
                    .catch(error => {
                        if (error.name === 'AbortError') {
                            // Request was aborted, ignore error
                            return;
                        }

                        console.error('Error loading products:', error);
                        this.isLoading = false;

                        // Only show alert for non-abort errors
                        if (error.name !== 'AbortError') {
                            alert('Failed to load products. Please try again.');
                        }
                    })
                    .finally(() => {
                        // Ensure loading state is cleared even if there's an error
                        if (controller === this.currentFetch) {
                            this.isLoading = false;
                        }
                    });
                },

                searchProducts() {
                    // Clear any existing timeout
                    clearTimeout(this.searchTimeout);

                    // Set a longer debounce for search (500ms is more appropriate)
                    this.searchTimeout = setTimeout(() => {
                        // If search term is empty, just load the current category
                        if (!this.searchTerm.trim()) {
                            this.loadProducts();
                            return;
                        }

                        this.isLoading = true;

                        // Check if we can do a client-side search first (for better performance)
                        if (this.allProducts && this.allProducts.length > 0 && this.allProducts.length < 200) {
                            // For small catalogs, client-side filtering is faster
                            const term = this.searchTerm.toLowerCase();
                            this.products = this.allProducts.filter(product =>
                                product.name_km.toLowerCase().includes(term) ||
                                product.name_en.toLowerCase().includes(term)
                            );
                            this.isLoading = false;
                        } else {
                            // For larger catalogs, use server-side search
                            const controller = new AbortController();
                            if (this.searchFetch && typeof this.searchFetch.abort === 'function') {
                                this.searchFetch.abort();
                            }
                            this.searchFetch = controller;

                            fetch(`/pos/products/search?term=${encodeURIComponent(this.searchTerm)}&category_id=${this.selectedCategory || ''}&_=${Date.now()}`, {
                                signal: controller.signal
                            })
                            .then(response => response.json())
                            .then(data => {
                                this.products = data;
                                this.isLoading = false;
                            })
                            .catch(error => {
                                if (error.name === 'AbortError') return;

                                console.error('Search error:', error);
                                this.isLoading = false;

                                // Fallback to client-side filtering as last resort
                                const term = this.searchTerm.toLowerCase();
                                this.products = this.allProducts.filter(product =>
                                    product.name_km.toLowerCase().includes(term) ||
                                    product.name_en.toLowerCase().includes(term)
                                );
                            });
                        }
                    }, 500);
                },

                openProductCustomization(product) {
                    if (!product) {
                        console.error('Attempted to open customization for undefined product');
                        return;
                    }

                    // Reset customization state
                    this.selectedProduct = product;
                    this.customQuantity = 1;
                    this.specialInstructions = '';
                    this.selectedSizeIndex = 0;
                    this.selectedToppings = [];
                    this.productSizes = [];
                    this.productToppings = [];

                    // Show the modal immediately for better UX
                    this.showCustomizationModal = true;

                    // Load product options in parallel
                    const promises = [];

                    // Fetch sizes if product has sizes
                    if (product.has_sizes) {
                        promises.push(
                            this.fetchProductSizes(product.id).catch(error => {
                                console.error('Error fetching sizes:', error);
                                return []; // Return empty array on error
                            })
                        );
                    }

                    // Fetch toppings if product has toppings
                    if (product.has_toppings) {
                        promises.push(
                            this.fetchProductToppings(product.id).catch(error => {
                                console.error('Error fetching toppings:', error);
                                return []; // Return empty array on error
                            })
                        );
                    }

                    // Wait for all promises with error handling
                    Promise.all(promises).catch(error => {
                        console.error('Error loading product options:', error);
                        alert('Failed to load product options. Please try again.');
                        this.showCustomizationModal = false;
                    });
                },

                fetchProductSizes(productId) {
                    if (!productId) {
                        console.error('No product ID provided for size fetch');
                        return Promise.resolve([]);
                    }

                    return fetch(`/pos/product-sizes/${productId}?_=${Date.now()}`)
                        .then(response => {
                            if (!response.ok) throw new Error(`Failed to load sizes. Status: ${response.status}`);
                            return response.json();
                        })
                        .then(data => {
                            // Handle potential null response
                            if (!data) {
                                console.warn('No sizes returned from server');
                                this.productSizes = [];
                                return [];
                            }

                            this.productSizes = Array.isArray(data) ? data : [];

                            // Initialize selected size to the first one if available
                            if (this.productSizes.length > 0) {
                                this.selectedSizeIndex = 0;
                            }

                            return data;
                        })
                        .catch(error => {
                            console.error('Error in fetchProductSizes:', error);
                            this.productSizes = [];
                            throw error;
                        });
                },

                fetchProductToppings(productId) {
                    return fetch(`/pos/product-toppings/${productId}?_=${Date.now()}`)
                        .then(response => response.json())
                        .then(data => {
                            this.productToppings = data || [];
                            // Initialize selectedToppings with correct length
                            this.selectedToppings = Array(this.productToppings.length).fill(false);
                            return data;
                        })
                        .catch(error => {
                            console.error('Error loading toppings:', error);
                            this.productToppings = [];
                            this.selectedToppings = [];
                            throw error;
                        });
                },

                removeFromCart(index, event) {
                    // Prevent event bubbling
                    if (event) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    try {
                        // Safety guard
                        if (!Array.isArray(this.cart)) {
                            console.warn('Cart is not an array when removing item');
                            this.cart = [];
                            return;
                        }

                        // Create a new array without the removed item for proper reactivity
                        const updatedCart = [...this.cart].filter((_, i) => i !== index);

                        // Update cart with new array - this creates a new reference for Alpine's reactivity
                        this.cart = updatedCart;

                        // Store in localStorage
                        localStorage.setItem('pos_cart', JSON.stringify(updatedCart));
                    } catch (error) {
                        console.error('Error removing item from cart:', error);
                        this.cart = [];
                    }
                },

                increaseQuantity(index) {
                    if (!Array.isArray(this.cart) || index < 0 || index >= this.cart.length) {
                        console.error('Invalid cart or index when increasing quantity');
                        return;
                    }

                    try {
                        // Create a completely new cart array for proper reactivity
                        const newCart = this.cart.map((item, i) => {
                            if (i === index) {
                                // Create a deep copy of the item to modify
                                const updatedItem = JSON.parse(JSON.stringify(item));
                                // Ensure quantity is a number and increment it
                                updatedItem.quantity = (parseInt(updatedItem.quantity) || 1) + 1;
                                return updatedItem;
                            }
                            return item;
                        });

                        // Replace the entire cart with the new one
                        this.cart = newCart;

                        // Update localStorage immediately
                        localStorage.setItem('pos_cart', JSON.stringify(this.cart));
                    } catch (error) {
                        console.error('Error increasing quantity:', error);
                    }
                },

                decreaseQuantity(index) {
                    if (!Array.isArray(this.cart) || index < 0 || index >= this.cart.length) {
                        console.error('Invalid cart or index when decreasing quantity');
                        return;
                    }

                    try {
                        const currentQuantity = parseInt(this.cart[index].quantity) || 1;

                        if (currentQuantity <= 1) {
                            // Remove the item if quantity would be less than 1
                            this.removeFromCart(index);
                            return;
                        }

                        // Create a completely new cart array for proper reactivity
                        const newCart = this.cart.map((item, i) => {
                            if (i === index) {
                                // Create a deep copy of the item to modify
                                const updatedItem = JSON.parse(JSON.stringify(item));
                                // Decrease quantity
                                updatedItem.quantity = currentQuantity - 1;
                                return updatedItem;
                            }
                            return item;
                        });

                        // Replace the entire cart with the new one
                        this.cart = newCart;

                        // Update localStorage immediately
                        localStorage.setItem('pos_cart', JSON.stringify(this.cart));
                    } catch (error) {
                        console.error('Error decreasing quantity:', error);
                    }
                },

                selectSize(index) {
                    if (index >= 0 && index < this.productSizes.length) {
                        this.selectedSizeIndex = index;
                    }
                },

                toggleTopping(index) {
                    // Initialize the array if it doesn't exist or isn't an array
                    if (!this.selectedToppings || !Array.isArray(this.selectedToppings)) {
                        this.selectedToppings = Array(this.productToppings.length).fill(false);
                    }

                    // If the array length doesn't match the toppings, recreate it
                    if (this.selectedToppings.length !== this.productToppings.length) {
                        this.selectedToppings = Array(this.productToppings.length).fill(false);
                    }

                    // Safety check
                    if (index >= 0 && index < this.selectedToppings.length) {
                        // Create a new array to ensure reactivity
                        const newToppings = [...this.selectedToppings];
                        newToppings[index] = !newToppings[index];
                        this.selectedToppings = newToppings;
                    }
                },

                // Update the "getBasePrice" function to use calculated price or base price
                getBasePrice() {
                    if (!this.selectedProduct) return 0;

                    // If product has sizes and a size is selected
                    if (this.selectedProduct.has_sizes &&
                        this.productSizes.length > 0 &&
                        this.selectedSizeIndex >= 0 &&
                        this.selectedSizeIndex < this.productSizes.length) {

                        const selectedSize = this.productSizes[this.selectedSizeIndex];

                        // Use the price directly from the size if available, otherwise calculate it
                        if (selectedSize.price) {
                            return parseFloat(selectedSize.price);
                        } else if (selectedSize.multiplier) {
                            // Apply multiplier to base price if no direct price is available
                            return parseFloat(this.selectedProduct.base_price) * parseFloat(selectedSize.multiplier);
                        }
                    }

                    // Default: return the product's base price
                    return parseFloat(this.selectedProduct.base_price);
                },

                get selectedToppingsCost() {
                    let cost = 0;
                    if (this.selectedToppings && this.productToppings && Array.isArray(this.selectedToppings)) {
                        this.selectedToppings.forEach((selected, index) => {
                            if (selected && this.productToppings[index]) {
                                cost += parseFloat(this.productToppings[index].price || 0);
                            }
                        });
                    }
                    return cost;
                },

                computeCurrentPrice() {
                    // Get base price with size adjustment
                    const basePrice = this.getBasePrice();

                    // Add toppings cost
                    const totalPrice = basePrice + this.selectedToppingsCost;

                    // Return formatted price
                    return totalPrice.toFixed(2);
                },

                addCustomizedProductToCart() {
                    if (!this.selectedProduct) return;

                    try {
                        // Haptic feedback for mobile devices
                        if (window.navigator && window.navigator.vibrate) {
                            window.navigator.vibrate(50);
                        }

                        let selectedSize = null;
                        if (this.selectedProduct.has_sizes && this.productSizes.length > 0 &&
                            this.selectedSizeIndex >= 0 && this.selectedSizeIndex < this.productSizes.length) {
                            selectedSize = this.productSizes[this.selectedSizeIndex];
                        }

                        const selectedToppingsList = this.productToppings.filter((topping, index) =>
                            Array.isArray(this.selectedToppings) &&
                            index < this.selectedToppings.length &&
                            this.selectedToppings[index]
                        );

                        // Create item object with all necessary data
                        const item = {
                            id: this.selectedProduct.id,
                            name_km: this.selectedProduct.name_km,
                            name_en: this.selectedProduct.name_en,
                            price: parseFloat(this.computeCurrentPrice()),
                            quantity: this.customQuantity,
                            image: this.selectedProduct.image,
                            size: selectedSize ? {
                                id: selectedSize.size_id,
                                name_km: selectedSize.size_name_km,
                                name_en: selectedSize.size_name_en,
                                price: parseFloat(selectedSize.price)
                            } : null,
                            toppings: selectedToppingsList.map(topping => ({
                                id: topping.topping_id,
                                name_km: topping.topping_name_km,
                                name_en: topping.topping_name_en,
                                price: parseFloat(topping.price)
                            })),
                            special_instructions: (this.specialInstructions || '').trim(),
                            added_at: new Date().toISOString() // For sorting by time added
                        };

                        // Add to cart with immutable update pattern
                        this.cart = [...this.cart, item];

                        // Ensure localStorage is updated
                        localStorage.setItem('pos_cart', JSON.stringify(this.cart));

                        // Close modal
                        this.showCustomizationModal = false;

                        // On mobile, show cart after adding item
                        if (window.innerWidth < 768) {
                            this.mobileTab = 'cart';
                        }
                    } catch (error) {
                        console.error('Error adding item to cart:', error);
                        alert('Failed to add item to cart. Please try again.');
                    }
                },

                get subtotal() {
                    // Ensure cart is an array
                    const cart = Array.isArray(this.cart) ? this.cart : [];

                    if (cart.length === 0) {
                        return 0;
                    }

                    return cart.reduce((sum, item) => {
                        // Guard against malformed cart items
                        if (!item) return sum;

                        const price = parseFloat(item.price) || 0;
                        const quantity = parseInt(item.quantity) || 1;
                        return sum + (price * quantity);
                    }, 0);
                },

                get total() {
                    return this.subtotal - this.discountAmount;
                },

                // Convert Riel to USD
                get cashAmountRielInUSD() {
                    return this.cashAmountRiel / this.exchangeRate;
                },

                // Calculate change in USD
                get changeUSD() {
                    const totalReceived = parseFloat(this.cashAmount) + this.cashAmountRielInUSD;
                    const change = totalReceived - this.total;
                    return change > 0 ? change : 0;
                },

                // Calculate change in KHR
                get changeKHR() {
                    return this.changeUSD * this.exchangeRate;
                },

                get canCompleteOrder() {
                    if (this.cart.length === 0) return false;

                    if (this.paymentMethod === 'cash') {
                        const totalReceived = parseFloat(this.cashAmount) + this.cashAmountRielInUSD;
                        return totalReceived >= this.total;
                    }
                    return this.paymentMethod !== '';
                },

                async loadExchangeRate() {
                    try {
                        const response = await fetch('/api/exchange-rates/current');
                        const data = await response.json();
                        if (data.rate) {
                            this.exchangeRate = parseFloat(data.rate);
                        }
                    } catch (error) {
                        console.error('Error loading exchange rate:', error);
                    }
                },

                async loadDiscount() {
                    try {
                        const response = await fetch('/api/discounts/applicable?amount=' + this.subtotal);
                        const data = await response.json();
                        if (data.discount) {
                            this.discount = data.discount;
                            this.calculateDiscountAmount();
                        }
                    } catch (error) {
                        console.error('Error loading discount:', error);
                    }
                },

                calculateDiscountAmount() {
                    if (!this.discount) {
                        this.discountAmount = 0;
                        return;
                    }

                    if (this.discount.type === 'percentage') {
                        this.discountAmount = Math.min(
                            (this.subtotal * this.discount.amount) / 100,
                            this.discount.max_discount || Infinity
                        );
                    } else {
                        this.discountAmount = Math.min(
                            this.discount.amount,
                            this.subtotal
                        );
                    }
                },

                setCashAmount(amount) {
                    this.cashAmount = amount;
                    this.cashAmountRiel = 0;
                },

                setCashAmountRiel(amount) {
                    this.cashAmountRiel = amount;
                    this.cashAmount = 0;
                },

                proceedToCheckout() {
                    if (this.cart.length === 0) return;

                    this.loadExchangeRate();
                    this.loadDiscount();
                    this.cashAmount = Math.ceil(this.total);
                    this.cashAmountRiel = 0;
                    this.showCheckoutModal = true;
                },

                async completeOrder() {
                    // Prevent multiple submissions
                    if (this.isProcessingOrder) {
                        console.log('Order already being processed');
                        return;
                    }

                    // Validate cart is not empty
                    if (!this.cart || this.cart.length === 0) {
                        alert('Cannot complete order with empty cart');
                        return;
                    }

                    // Additional validation to prevent $0.00 orders
                    if (this.total <= 0) {
                        alert('Order total must be greater than $0.00');
                        return;
                    }

                    this.isProcessingOrder = true;

                    try {
                        // Existing code with the changes from above...

                        // Set a loading indicator (optional)
                        const orderBtn = document.getElementById('complete-order-btn');
                        if (orderBtn) {
                            orderBtn.innerText = 'Processing...';
                            orderBtn.disabled = true;
                        }

                        // Format order data with proper validation and structure
                        const orderData = {
                            items: this.cart.map(item => {
                                // Properly format toppings to match database schema requirements
                                const toppings = Array.isArray(item.toppings)
                                    ? item.toppings.map(topping => ({
                                        product_topping_id: parseInt(topping.id), // Ensure this is a number
                                        price: parseFloat(topping.price || 0)
                                    }))
                                    : [];

                                return {
                                    product_id: parseInt(item.id),
                                    product_size_id: item.size?.id ? parseInt(item.size.id) : null,
                                    quantity: parseInt(item.quantity || 1),
                                    unit_price: parseFloat(item.price || 0),
                                    price: parseFloat(item.price || 0),
                                    toppings: toppings,
                                    special_instructions: item.special_instructions || ''
                                };
                            }),
                            payment: {
                                method: this.paymentMethod,
                                amount_usd: this.paymentMethod === 'cash'
                                    ? parseFloat(this.cashAmount || 0)
                                    : parseFloat(this.total),
                                amount_khr: this.paymentMethod === 'cash'
                                    ? parseFloat(this.cashAmountRiel || 0)
                                    : 0,
                                exchange_rate: parseFloat(this.exchangeRate || 4100)
                            },
                            discount_id: this.discount?.id ? parseInt(this.discount.id) : null,
                            subtotal: parseFloat(this.subtotal || 0),
                            discount_amount: parseFloat(this.discountAmount || 0),
                            total: parseFloat(this.total || 0)
                        };

                        console.log('Sending order data:', JSON.stringify(orderData));

                        // Send the request
                        const response = await fetch('/api/orders', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(orderData)
                        });

                        // Handle non-JSON responses
                        const contentType = response.headers.get("content-type");
                        let result;
                        if (contentType && contentType.indexOf("application/json") !== -1) {
                            result = await response.json();
                        } else {
                            const text = await response.text();
                            console.error('Non-JSON response:', text);
                            result = { message: 'Server returned a non-JSON response' };
                        }

                        if (!response.ok) {
                            throw new Error(result.message || 'Failed to create order');
                        }

                        this.orderCompleted = true;
                        this.showCheckoutModal = false;
                        localStorage.removeItem('pos_cart');
                    } catch (error) {
                        console.error('Error creating order:', error);
                        alert('Failed to process order: ' + error.message);
                    } finally {
                        // Always reset the processing flag, regardless of outcome
                        this.isProcessingOrder = false;

                        // Reset button if it exists
                        const orderBtn = document.getElementById('complete-order-btn');
                        if (orderBtn) {
                            orderBtn.innerText = 'Complete Order';
                            orderBtn.disabled = false;
                        }
                    }
                },

                printReceipt() {
                    try {
                        // Create a new window for the receipt
                        const printWindow = window.open('', '_blank', 'width=800,height=600');

                        // Get the current date and time
                        const now = new Date();
                        const dateStr = now.toLocaleDateString();
                        const timeStr = now.toLocaleTimeString();

                        // Generate the receipt HTML with proper styling
                        const receiptHTML = `
                            <!DOCTYPE html>
                            <html lang="en">
                            <head>
                                <meta charset="UTF-8">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <title>Receipt</title>
                                <style>
                                    body {
                                        font-family: 'Arial', sans-serif;
                                        margin: 0;
                                        padding: 20px;
                                        color: #333;
                                    }
                                    .receipt {
                                        max-width: 400px;
                                        margin: 0 auto;
                                        border: 1px solid #ddd;
                                        padding: 20px;
                                        box-shadow: 0 0 10px rgba(0,0,0,0.1);
                                    }
                                    .header {
                                        text-align: center;
                                        margin-bottom: 20px;
                                        border-bottom: 1px dashed #ccc;
                                        padding-bottom: 10px;
                                    }
                                    .logo {
                                        max-width: 100px;
                                        margin: 0 auto 10px;
                                    }
                                    .store-name {
                                        font-size: 24px;
                                        font-weight: bold;
                                    }
                                    .store-info {
                                        font-size: 12px;
                                        color: #666;
                                    }
                                    .receipt-details {
                                        margin-bottom: 20px;
                                        padding-bottom: 10px;
                                        border-bottom: 1px dashed #ccc;
                                        font-size: 12px;
                                    }
                                    .receipt-id {
                                        font-weight: bold;
                                    }
                                    .items {
                                        margin-bottom: 20px;
                                        width: 100%;
                                    }
                                    .items table {
                                        width: 100%;
                                        border-collapse: collapse;
                                        font-size: 14px;
                                    }
                                    .items th {
                                        text-align: left;
                                        font-weight: normal;
                                        color: #666;
                                        font-size: 12px;
                                        padding-bottom: 8px;
                                    }
                                    .items td {
                                        padding: 6px 0;
                                    }
                                    .item-price {
                                        text-align: right;
                                    }
                                    .item-qty {
                                        text-align: center;
                                    }
                                    .item-total {
                                        text-align: right;
                                    }
                                    .item-topping {
                                        font-size: 12px;
                                        color: #666;
                                        padding-left: 10px;
                                    }
                                    .summary {
                                        margin-top: 20px;
                                        border-top: 1px dashed #ccc;
                                        padding-top: 10px;
                                    }
                                    .summary-row {
                                        display: flex;
                                        justify-content: space-between;
                                        margin-bottom: 5px;
                                    }
                                    .summary-row.total {
                                        font-weight: bold;
                                        font-size: 16px;
                                        margin-top: 10px;
                                        border-top: 1px solid #333;
                                        padding-top: 5px;
                                    }
                                    .payment-info {
                                        margin-top: 20px;
                                        font-size: 12px;
                                    }
                                    .footer {
                                        margin-top: 30px;
                                        text-align: center;
                                        font-size: 12px;
                                        color: #666;
                                    }
                                    .secondary-text {
                                        font-size: 11px;
                                        color: #777;
                                    }
                                    @media print {
                                        body {
                                            padding: 0;
                                            margin: 0;
                                        }
                                        .receipt {
                                            box-shadow: none;
                                            border: none;
                                            padding: 10px;
                                        }
                                        .no-print {
                                            display: none;
                                        }
                                    }
                                </style>
                            </head>
                            <body>
                                <div class="receipt">
                                    <div class="header">
                                        <div class="store-name">Your Store Name</div>
                                        <div class="store-info">
                                            123 Main Street, City<br>
                                            Phone: (855) 123-456-789<br>
                                        </div>
                                    </div>

                                    <div class="receipt-details">
                                        <div>Date: ${dateStr}</div>
                                        <div>Time: ${timeStr}</div>
                                        <div>Receipt #: INV-${Math.floor(Math.random() * 100000)}</div>
                                        <div>Cashier: ${document.querySelector('meta[name="user-name"]')?.content || 'Staff'}</div>
                                    </div>

                                    <div class="items">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                    <th class="item-qty">Qty</th>
                                                    <th class="item-price">Price</th>
                                                    <th class="item-total">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${this.cart.map(item => `
                                                    <tr>
                                                        <td>
                                                            ${item.name_en}<br>
                                                            <span class="secondary-text">${item.name_km}</span>
                                                            ${item.size ? `<br><span class="secondary-text">Size: ${item.size.name_en}</span>` : ''}
                                                            ${(item.toppings && item.toppings.length > 0) ?
                                                                item.toppings.map(topping => `
                                                                    <div class="item-topping">+ ${topping.name_en} ($${topping.price.toFixed(2)})</div>
                                                                `).join('') : ''
                                                            }
                                                        </td>
                                                        <td class="item-qty">${item.quantity}</td>
                                                        <td class="item-price">$${parseFloat(item.price).toFixed(2)}</td>
                                                        <td class="item-total">$${(item.price * item.quantity).toFixed(2)}</td>
                                                    </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="summary">
                                        <div class="summary-row">
                                            <div>Subtotal:</div>
                                            <div>$${this.subtotal.toFixed(2)}</div>
                                        </div>

                                        ${this.discount ? `
                                            <div class="summary-row">
                                                <div>Discount (${this.discount.name_en}):</div>
                                                <div>-$${this.discountAmount.toFixed(2)}</div>
                                            </div>
                                        ` : ''}

                                        <div class="summary-row total">
                                            <div>Total:</div>
                                            <div>$${this.total.toFixed(2)}</div>
                                        </div>

                                        <div class="summary-row secondary-text">
                                            <div>Riel (៛):</div>
                                            <div>៛${Math.round(this.total * this.exchangeRate).toLocaleString()}</div>
                                        </div>
                                    </div>

                                    <div class="payment-info">
                                        <div><strong>Payment Method:</strong> ${this.paymentMethod.charAt(0).toUpperCase() + this.paymentMethod.slice(1)}</div>
                                        ${this.paymentMethod === 'cash' ? `
                                            <div><strong>Amount Received:</strong> $${parseFloat(this.cashAmount).toFixed(2)}${
                                                this.cashAmountRiel > 0 ? ` + ៛${parseInt(this.cashAmountRiel).toLocaleString()}` : ''
                                            }</div>
                                            <div><strong>Change:</strong> $${this.changeUSD.toFixed(2)} / ៛${Math.round(this.changeKHR).toLocaleString()}</div>
                                        ` : ''}
                                    </div>

                                    <div class="footer">
                                        <p>Thank you for your purchase!</p>
                                        <p>Exchange Rate: $1 = ៛${this.exchangeRate}</p>
                                    </div>
                                </div>

                                <div class="no-print" style="text-align: center; margin-top: 20px;">
                                    <button onclick="window.print()" style="padding: 10px 20px; background: #d97706; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                        Print Receipt
                                    </button>
                                </div>
                            </body>
                            </html>
                        `;

                        // Write to the new window and prepare it for printing
                        printWindow.document.open();
                        printWindow.document.write(receiptHTML);
                        printWindow.document.close();

                        // Wait for content to load before triggering print
                        printWindow.addEventListener('load', function() {
                            // Automatically open print dialog in the new window
                            setTimeout(() => {
                                printWindow.print();
                                // Don't close the window after print dialog - user can close it manually
                            }, 500);
                        });
                    } catch (error) {
                        console.error('Error printing receipt:', error);
                        alert('Failed to print receipt. Please try again.');
                        // Fallback to the regular print method if there's an error
                        window.print();
                    }
                },

                resetApp() {
                    this.cart = [];
                    this.orderCompleted = false;
                    this.paymentMethod = 'cash';
                    this.cashAmount = 0;
                    this.cashAmountRiel = 0;
                    this.discount = null;
                    this.discountAmount = 0;
                    localStorage.removeItem('pos_cart');

                    // Reset to products view if on mobile
                    if (window.innerWidth < 768) {
                        this.mobileTab = 'products';
                    }
                }
            };
        }
    </script>

    <!-- Add this just before your closing body tag -->
    <script>
        // Add global hook to properly cleanup Alpine components before Livewire replaces them
        document.addEventListener('livewire:load', function() {
            // Create a backup of the cart before Livewire updates
            let cartBackup = null;

            Livewire.hook('message.sent', () => {
                try {
                    // Before Livewire updates, back up the cart
                    const alpineComponent = document.querySelector('[x-data]')?.__x;
                    if (alpineComponent && alpineComponent.getUnobservedData) {
                        const data = alpineComponent.getUnobservedData();
                        if (data && Array.isArray(data.cart)) {
                            cartBackup = [...data.cart];
                        }
                    }
                } catch (e) {
                    console.error('Error backing up cart:', e);
                }
            });

            Livewire.hook('message.processed', () => {
                try {
                    // After Livewire updates, restore the cart if needed
                    if (cartBackup) {
                        const alpineComponent = document.querySelector('[x-data]')?.__x;
                        if (alpineComponent && alpineComponent.updateUnobservedData) {
                            alpineComponent.updateUnobservedData(data => {
                                if (!data.cart || !Array.isArray(data.cart)) {
                                    data.cart = cartBackup;
                                }
                            });
                        }
                    }
                } catch (e) {
                    console.error('Error restoring cart:', e);
                }
            });

            // Fix the element removal hook
            Livewire.hook('element.removed', (el) => {
                if (!window.Alpine) return;

                // Use a more robust cleanup approach
                setTimeout(() => {
                    try {
                        const components = el.querySelectorAll('[x-data]');
                        components.forEach(component => {
                            if (component._x_dataStack) {
                                delete component._x_dataStack;
                            }
                            if (component.__x) {
                                component.__x = null;
                            }
                        });
                    } catch (e) {
                        console.error('Error during Alpine cleanup:', e);
                    }
                }, 0);
            });
        });
    </script>
</x-pos-layout>
