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

    <div class="flex space-x-2">
        <button
            @click="proceedToCheckout()"
            class="flex-1 bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition font-medium"
            :disabled="cart.length === 0"
            :class="{'opacity-50 cursor-not-allowed': cart.length === 0}"
        >
            <span x-show="cart.length > 0">Checkout</span>
            <span x-show="cart.length === 0">No Items</span>
        </button>

        <button
            @click="markAsPending()"
            class="flex-1 bg-red-500 text-white py-3 px-4 rounded-lg hover:bg-red-600 transition font-medium"
            :disabled="cart.length === 0"
            :class="{'opacity-50 cursor-not-allowed': cart.length === 0}"
        >
            <span x-show="cart.length > 0">Pending</span>
            <span x-show="cart.length === 0">No Items</span>
        </button>
    </div>
</div>
