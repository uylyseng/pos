<div>
    <div x-show="$wire.showModal"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         x-on:click.self="$wire.closeModal()" x-cloak>
        <div x-show="$wire.showModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div class="p-4 border-b flex justify-between items-center sticky top-0 bg-white z-10">
                <div>
                    <h3 class="font-bold text-lg">
                        <span>{{ $product ? $product->name_km : '' }}</span>
                        <span class="text-sm ml-2">{{ $product ? $product->name_en : '' }}</span>
                    </h3>
                    @if($editMode)
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded animate-pulse">Editing</span>
                    @endif
                </div>
                <button type="button" wire:click.prevent="closeModal" class="text-gray-500 hover:text-gray-700 transition-colors duration-200 transform hover:scale-110">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body with Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-4" x-data @submit.prevent>
                @if($product)
                    <!-- Product Image and Base Info -->
                    <div class="flex items-center mb-6 hover:shadow-md transition-shadow duration-300 p-2 rounded-lg">
                        <div class="h-20 w-20 rounded-lg bg-gray-100 overflow-hidden mr-4 transition-transform duration-300 transform hover:scale-105">
                            @if($product->image)
                                <img src="{{ asset('storage/' . str_replace('public/', '', $product->image)) }}"
                                     alt="{{ $product->name_en }}"
                                     class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full bg-gray-200 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm text-gray-500">{{ $product->description }}</h4>
                            <div class="text-green-600 font-bold mt-1 transition-all duration-300 hover:text-green-700">${{ number_format($this->getCurrentPrice(), 2) }}</div>
                        </div>
                    </div>

                    <!-- Size Selection -->
                    @if($product->has_sizes && count($sizes) > 0)
                        <div class="mb-6" x-data="{ visible: false }" x-init="setTimeout(() => visible = true, 100)">
                            <h4 class="font-medium mb-2">Select Size</h4>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($sizes as $index => $size)
                                    <button
                                        type="button"
                                        wire:click.prevent="selectSize({{ $index }})"
                                        wire:loading.attr="disabled"
                                        x-show="visible"
                                        x-transition:enter="transition ease-out duration-300 delay-{{ 100 * $index }}"
                                        x-transition:enter-start="opacity-0 transform translate-y-4"
                                        x-transition:enter-end="opacity-100 transform translate-y-0"
                                        class="border rounded-lg p-3 flex justify-between items-center transition-all duration-200 hover:shadow-md
                                              {{ $selectedSizeIndex === $index ? 'border-green-600 bg-green-50' : 'border-gray-200 hover:border-gray-300' }}"
                                    >
                                        <div>
                                            <div class="font-medium">{{ $size['name_km'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $size['name_en'] }}</div>
                                        </div>
                                        <div class="text-green-600">${{ number_format($product->base_price * $size['multiplier'], 2) }}</div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Toppings Selection -->
                    @if($product->has_toppings && count($toppings) > 0)
                        <div class="mb-6" x-data="{ visible: false }" x-init="setTimeout(() => visible = true, 200)">
                            <h4 class="font-medium mb-2">Add Toppings <span class="text-xs text-gray-500">(Select multiple)</span></h4>
                            @foreach($toppings as $index => $topping)
                                <div
                                    x-show="visible"
                                    x-transition:enter="transition ease-out duration-300 delay-{{ 50 * $index }}"
                                    x-transition:enter-start="opacity-0 transform translate-x-4"
                                    x-transition:enter-end="opacity-100 transform translate-x-0"
                                    class="border-b border-gray-100 py-2 last:border-b-0 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="flex items-center cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    wire:model.live="selectedToppings.{{ $index }}"
                                                    class="form-checkbox h-5 w-5 text-green-600 rounded transition-all duration-200"
                                                >
                                                <span class="ml-2">{{ $topping['name_km'] }}</span>
                                                <span class="text-xs text-gray-500 ml-2">{{ $topping['name_en'] }}</span>
                                            </label>
                                        </div>
                                        <div class="text-green-600">+ ${{ number_format($topping['price'], 2) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Quantity Control -->
                    <div class="mb-6" x-data="{ visible: false }" x-init="setTimeout(() => visible = true, 300)">
                        <h4 class="font-medium mb-2">Quantity</h4>
                        <div
                            x-show="visible"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            class="flex items-center border border-gray-300 rounded-md w-min">
                            <button
                                type="button"
                                wire:click.prevent="decreaseQuantity"
                                wire:loading.attr="disabled"
                                wire:target="decreaseQuantity"
                                class="px-4 py-2 text-lg border-r border-gray-300 hover:bg-red-100 focus:bg-red-200 transition-all duration-200
                                      {{ $quantity > 1 ? 'text-red-500' : 'text-gray-300 cursor-not-allowed' }}"
                                @disabled($quantity <= 1)
                            >-</button>
                            <div class="w-12 text-center py-2 bg-gray-50 font-medium">
                                <span wire:loading.class="animate-pulse" wire:target="decreaseQuantity, increaseQuantity">{{ $quantity }}</span>
                            </div>
                            <button
                                type="button"
                                wire:click.prevent="increaseQuantity"
                                wire:loading.attr="disabled"
                                wire:target="increaseQuantity"
                                class="px-4 py-2 text-lg border-l border-gray-300 hover:bg-green-100 focus:bg-green-200 transition-all duration-200 text-green-500"
                            >+</button>
                        </div>
                    </div>

                    <!-- Special Instructions (optional) -->
                    <div class="mb-6" x-data="{ visible: false }" x-init="setTimeout(() => visible = true, 400)">
                        <h4 class="font-medium text-gray-600 mb-2">Special Instructions (optional)</h4>
                        <textarea
                            x-show="visible"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            wire:model.live="specialInstructions"
                            class="w-full border border-gray-300 rounded-lg focus:border-green-500 focus:ring focus:ring-gray-500 focus:ring-opacity-50 transition-all duration-200"
                            rows="4"
                            placeholder="Any special requests..."
                        ></textarea>
                    </div>
                @endif
            </div>

            <!-- Modal Footer with Actions -->
            <div class="p-4 border-t bg-gray-50" x-data="{ visible: false }" x-init="setTimeout(() => visible = true, 500)">
                <!-- Price Summary -->
                <div
                    x-show="visible"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    class="bg-gray-50 p-4 rounded-lg hover:shadow-inner transition-shadow duration-300">
                    <div class="flex justify-between mb-2">
                        <span>Base Price:</span>
                        <span>${{ number_format($this->getBasePrice(), 2) }}</span>
                    </div>
                    @if($this->getToppingsCost() > 0)
                        <div class="flex justify-between mb-2">
                            <span>Toppings:</span>
                            <span>${{ number_format($this->getToppingsCost(), 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between font-bold">
                        <span>Item Total:</span>
                        <span class="transition-all duration-300 hover:text-green-600">${{ number_format($this->getCurrentPrice() * $quantity, 2) }}</span>
                    </div>
                </div>

                <button
                    type="button"
                    wire:click.prevent="addToCart"
                    wire:loading.attr="disabled"
                    wire:target="addToCart"
                    x-show="visible"
                    x-transition:enter="transition ease-out duration-300 delay-100"
                    x-transition:enter-start="opacity-0 transform translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-all duration-300 font-medium relative hover:shadow-md hover:scale-[1.02] transform"
                >
                    <div wire:loading.remove wire:target="addToCart">
                        {{ $editMode ? 'Update' : 'Add to cart' }}
                    </div>
                    <div wire:loading wire:target="addToCart" class="flex items-center justify-center">
                        <div class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Processing...</span>
                        </div>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>
