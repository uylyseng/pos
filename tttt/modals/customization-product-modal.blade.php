<div x-show="showCustomizationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-cloak>
<div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col" @click.away="showCustomizationModal = false">
    <!-- Modal Header -->
    <div class="p-4 border-b flex justify-between items-center sticky top-0 bg-white z-10">
        <div>
            <h3 class="font-bold text-lg">
            <span x-text="selectedProduct ? selectedProduct.name_km : ''"></span>
            <span class="text-sm ml-2" x-text="selectedProduct ? selectedProduct.name_en :''"></span>
            </h3>
        </div>
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
                <h4 class="text-sm text-gray-500" x-text="selectedProduct ? selectedProduct.description : ''"></h4>
                <div class="text-green-600 font-bold mt-1" x-text="selectedProduct ? '$' + computeCurrentPrice() : ''"></div>
            </div>
        </div>

        <!-- Size Selection -->
        <div x-show="selectedProduct && selectedProduct.has_sizes && productSizes.length > 0" class="mb-6">
            <h4 class="font-medium mb-2">Select Size</h4>
            <div class="grid grid-cols-2 gap-2">
                <template x-for="(size, index) in productSizes" :key="index">
                    <button
                        class="border rounded-lg p-3 flex justify-between items-center transition-all"
                        :class="selectedSizeIndex === index ? 'border-green-600 bg-green-50' : 'border-gray-200 hover:border-gray-300'"
                        @click="selectSize(index)"
                    >
                        <div>
                            <div class="font-medium" x-text="size.size_name_km"></div>
                            <div class="text-xs text-gray-500" x-text="size.size_name_en"></div>
                        </div>
                        <div class="text-green-600" x-text="'$' + (selectedProduct.base_price * size.multiplier).toFixed(2)"></div>
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
                                    class="form-checkbox h-5 w-5 text-green-600 rounded"
                                    :checked="selectedToppings && selectedToppings[index]"
                                    @click="toggleTopping(index)"
                                >
                                <span class="ml-2" x-text="topping.topping_name_km"></span>
                                <span class="text-xs text-gray-500 ml-2" x-text="topping.topping_name_en"></span>
                            </label>
                        </div>
                        <div class="text-green-600" x-text="'+ $' + topping.price"></div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Quantity Control -->
        <div class="mb-6">
            <h4 class="font-medium mb-2">Quantity</h4>
            <div class="flex items-center border border-gray-300 rounded-md w-min">
            <button
                @click="customQuantity > 1 ? customQuantity-- : null"
                class="px-4 py-2 text-lg border-r border-gray-300 hover:bg-red-100 focus:bg-red-200 transition"
                :class="{'text-red-500': customQuantity > 1}"
            >-</button>
            <div class="w-12 text-center py-2 bg-gray-50 font-medium">
                <span x-text="customQuantity"></span>
            </div>
            <button
                @click="customQuantity++"
                class="px-4 py-2 text-lg border-l border-gray-300 hover:bg-green-100 focus:bg-green-200 transition text-green-500"
            >+</button>
            </div>
        </div>

        <!-- Special Instructions (optional) -->
        <div class="mb-6">
            <h4 class="font-medium text-gray-600 mb-2">Special Instructions (optional)</h4>
            <textarea
            x-model="specialInstructions"
            class="w-full border border-gray-300 rounded-lg focus:border-green-500 focus:ring focus:ring-gray-500 focus:ring-opacity-50"
            rows="4"
            placeholder="Any special requests..."
            ></textarea>
        </div>


    </div>

    <!-- Modal Footer with Actions -->
    <div class="p-4 border-t bg-gray-50">

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

        <button
            @click="addCustomizedProductToCart"
            class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition font-medium"
        >
            Add
        </button>
    </div>
</div>
</div>
