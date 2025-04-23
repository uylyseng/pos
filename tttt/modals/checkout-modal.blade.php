@php
    $paymentMethods = \App\Models\PaymentMethod::active()->orderByName()->get();
@endphp

<!-- Checkout Modal -->
<div x-show="showCheckoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-cloak @keydown.escape.window="showCheckoutModal = false">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Modal Header -->
        <div class="p-4 border-b flex justify-between items-center sticky top-0 bg-white z-10">
            <h3 class="font-bold text-lg">Complete Order</h3>
            <button @click="showCheckoutModal = false" class="text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body with Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-4">
            <!-- Order Summary -->
            <div class="mb-6">
                <h4 class="font-medium mb-2">Order Summary</h4>
                <div class="bg-gray-100 rounded-lg p-3 mb-3">
                    <div class="flex justify-between mb-1">
                        <span>Subtotal</span>
                        <span x-text="'$' + subtotal.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between mb-1" x-show="discountAmount > 0">
                        <span>Discount</span>
                        <span x-text="'-$' + discountAmount.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between font-bold mt-2 pt-2 border-t border-gray-300">
                        <span>Total</span>
                        <span x-text="'$' + total.toFixed(2)"></span>
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="mb-6">
                <label for="payment-method" class="block text-sm font-medium mb-2">Payment Method</label>
                <select
                    id="payment-method"
                    x-model="paymentMethod"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50 mb-3"
                >
                    @foreach($paymentMethods as $method)
                        <option value="{{ strtolower($method->name_en) }}">{{ $method->display_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Cash Payment Form (shown when payment method is cash) -->
            <div x-show="paymentMethod === 'cash'" class="mb-6">
                <!-- USD Input -->
                <div class="mb-3">
                    <label for="cash-amount" class="block text-sm font-medium mb-2">Cash Amount (USD)</label>
                    <div class="flex items-center">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">$</span>
                            </div>
                            <input
                                type="number"
                                id="cash-amount"
                                min="0"
                                step="0.01"
                                x-model.number="cashAmount"
                                class="block w-full pl-7 pr-12 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-amber-500 focus:ring-opacity-50 focus:border-amber-500"
                                :placeholder="total.toFixed(2)"
                                @input="cashAmountRiel = 0"
                            >
                        </div>

                        <!-- Quick cash buttons -->
                        <div class="flex ml-2 space-x-1">
                            <button
                                @click="setCashAmount(total)"
                                class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300 text-sm"
                                x-text="'$' + total.toFixed(0)"
                            ></button>
                            <button
                                @click="setCashAmount(Math.ceil(total/5)*5)"
                                class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300 text-sm"
                                x-text="'$' + Math.ceil(total/5)*5"
                            ></button>
                        </div>
                    </div>
                </div>

                <!-- KHR Input (Optional for cash) -->
                <div class="mb-3">
                    <label for="cash-amount-riel" class="block text-sm font-medium mb-2">Cash Amount (KHR)</label>
                    <div class="flex items-center">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">៛</span>
                            </div>
                            <input
                                type="number"
                                id="cash-amount-riel"
                                min="0"
                                step="100"
                                x-model.number="cashAmountRiel"
                                class="block w-full pl-7 pr-12 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-amber-500 focus:ring-opacity-50 focus:border-amber-500"
                                placeholder="0"
                                @input="cashAmount = 0"
                            >
                        </div>

                        <!-- Quick cash buttons for KHR -->
                        <div class="flex ml-2 space-x-1">
                            <button
                                @click="setCashAmountRiel(Math.ceil(total * exchangeRate / 1000) * 1000)"
                                class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300 text-sm"
                                x-text="'៛' + Math.ceil(total * exchangeRate / 1000) * 1000"
                            ></button>
                            <button
                                @click="setCashAmountRiel(Math.ceil(total * exchangeRate / 5000) * 5000)"
                                class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300 text-sm"
                                x-text="'៛' + Math.ceil(total * exchangeRate / 5000) * 5000"
                            ></button>
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
            </div>

            <!-- Card or Mobile Payment Form -->
            <div x-show="paymentMethod !== 'cash'" class="mb-6">
                <p class="text-center text-gray-500 py-4">
                    Process payment using <span x-text="paymentMethod === 'card' ? 'card terminal' : 'mobile payment app'"></span>.
                </p>
            </div>
        </div>

        <!-- Modal Footer with Actions -->
        <div class="p-4 border-t bg-gray-50">
            <button
                @click="completeOrder"
                :disabled="!canCompleteOrder || isProcessingOrder"
                class="w-full bg-amber-600 text-white py-3 px-4 rounded-lg hover:bg-amber-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                x-text="isProcessingOrder ? 'Processing...' : 'Complete Order'"
            ></button>
        </div>
    </div>
</div>
