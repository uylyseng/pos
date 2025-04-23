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
