<div class="border-t p-4 bg-white shadow-md rounded-lg">
    <div x-show="$wire.hasItems"
         x-transition:enter="transition-opacity duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="flex justify-between items-center mb-2 text-sm text-gray-600">
            <span>Subtotal:</span>
            <span>${{ number_format($subtotal, 2) }}</span>
        </div>
        <div class="flex justify-between items-center mb-4 text-lg font-bold">
            <span>Total:</span>
            <span>${{ number_format($total, 2) }}</span>
        </div>
    </div>

    @if($errorMessage)
    <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-4 text-sm animate-pulse">
        {{ $errorMessage }}
    </div>
    @endif

    <div>
        <button
            wire:click="proceedToCheckout"
            wire:loading.attr="disabled"
            wire:target="proceedToCheckout"
            class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition duration-300 transform hover:scale-105 active:scale-95 font-medium"
            @class([
            'opacity-50 cursor-not-allowed' => !$hasItems || $processingAction
            ])
            @disabled(!$hasItems || $processingAction)
        >
            <div wire:loading.remove wire:target="proceedToCheckout" class="flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span x-show="$wire.hasItems">Checkout</span>
            <span x-show="!$wire.hasItems">No Items</span>
            </div>
            <div wire:loading wire:target="proceedToCheckout" class="flex items-center justify-center">
                <div class="flex items-center justify-center">
                    <svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Processing</span>
                </div>
            </div>
        </button>
    </div>

    <!-- Notification Toast with improved animations -->
    <div
        x-data="{ show: false, type: 'success', message: '' }"
        @notify.window="
            show = true;
            type = $event.detail.type;
            message = $event.detail.message;
            setTimeout(() => { show = false }, 3000);
        "
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-8"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-8"
        class="fixed bottom-4 right-4 p-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 animate-bounce-in"
        :class="type === 'success' ? 'bg-green-50 border-l-4 border-green-500' : 'bg-red-50 border-l-4 border-red-500'"
        style="display: none;"
    >
        <div class="shrink-0">
            <template x-if="type === 'success'">
                <svg class="h-6 w-6 text-green-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </template>
            <template x-if="type === 'error'">
                <svg class="h-6 w-6 text-red-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </template>
        </div>
        <div :class="type === 'success' ? 'text-green-800' : 'text-red-800'">
            <p class="font-medium" x-text="message"></p>
        </div>
        <button @click="show = false" class="ml-auto text-gray-400 hover:text-gray-500 transition transform hover:rotate-90 duration-200">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>
