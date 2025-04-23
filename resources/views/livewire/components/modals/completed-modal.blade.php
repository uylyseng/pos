<div>
    <!-- Order Completed Modal -->
    <div x-show="$wire.showModal"
         class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 p-4"
         x-cloak
         @keydown.escape.window="$wire.closeModal()">
        <div class="bg-white p-6 rounded-lg max-w-md w-full shadow-xl"
             @click.away="$wire.closeModal()">
            <div class="text-center mb-6">
                <div class="mx-auto rounded-full bg-green-100 p-3 h-24 w-24 flex items-center justify-center">
                    <svg class="h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold mt-4">Order Complete!</h2>
                <p class="text-gray-600 mt-2">Your order has been processed successfully.</p>
                @if($orderId)
                    <p class="text-gray-500 text-sm mt-1">Order #ORD{{ str_pad($orderId, 4, '0', STR_PAD_LEFT) }}</p>
                @endif
                @if($userName)
                    <p class="text-gray-500 text-sm">Served by: {{ $userName }}</p>
                @endif
            </div>

            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-600">Order Total:</span>
                    <span class="font-bold">
                        ${{ number_format($orderTotal, 2) }} / ៛{{ number_format(round($orderTotal * $exchangeRate)) }}
                    </span>
                </div>
                @if($paymentMethod === 'cash')
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Amount Received:</span>
                        <span>
                            @if($cashAmountUsd > 0)
                                ${{ number_format($cashAmountUsd, 2) }}
                            @endif
                            @if($cashAmountRiel > 0)
                                @if($cashAmountUsd > 0) + @endif
                                  ៛{{ number_format($cashAmountRiel) }}
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-t">
                        <span class="text-gray-600">Change:</span>
                        <div>
                            <span>${{ number_format($changeUsd, 2) }}</span>
                            <span class="block text-sm text-gray-500">៛{{ number_format(round($changeKhr)) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button
                    wire:click="printReceipt"
                    wire:loading.attr="disabled"
                    wire:target="printReceipt"
                    class="py-3 px-4 bg-gray-200 rounded-lg hover:bg-gray-300 transition flex items-center justify-center relative"
                >
                    <div wire:loading.remove wire:target="printReceipt" class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Receipt
                    </div>
                    <div wire:loading wire:target="printReceipt" class="flex items-center">
                        <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Printing...
                    </div>
                </button>
                <button
                    wire:click="newOrder"
                    wire:loading.attr="disabled"
                    wire:target="newOrder"
                    class="py-3 px-4 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition flex items-center justify-center"
                >
                    <div wire:loading.remove wire:target="newOrder" class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        New Order
                    </div>
                    <div wire:loading wire:target="newOrder" class="flex items-center">
                        <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript to handle printing -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('print-receipt', (data) => {
                try {
                    // You might want to open a print window or redirect to a print page
                    window.open(`/receipt/${data.orderId}`, '_blank');
                } catch (e) {
                    console.error('Failed to open print dialog:', e);
                }
            });
        });
    </script>
</div>
