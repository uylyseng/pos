<?php

namespace App\Livewire\Components\Carts;

use Livewire\Component;
use Livewire\Attributes\On;

class CheckoutCart extends Component
{
    public $cartItems = [];
    public $subtotal = 0;
    public $total = 0;
    public $hasItems = false;
    public $processingAction = false;
    public $errorMessage = null;

    public function mount()
    {
        $this->updateCart();
    }

    #[On('cartUpdated')]
    #[On('itemAdded')]
    #[On('itemRemoved')]
    #[On('itemUpdated')]
    #[On('cartCleared')]
    public function updateCart($cartItems = null)
    {
        if ($cartItems !== null) {
            $this->cartItems = $cartItems;
        } else {
            $this->cartItems = session('cart', []);
        }

        $this->calculateTotals();
        $this->hasItems = count($this->cartItems) > 0;
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;

        foreach ($this->cartItems as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }

        // Apply any additional calculations (tax, discounts, etc.) here
        $this->total = $this->subtotal;
    }

    public function proceedToCheckout()
    {
        if (!$this->hasItems) {
            return;
        }

        $this->processingAction = true;
        $this->errorMessage = null;

        try {
            // Dispatch event to parent component to handle checkout process
            $this->dispatch('initiateCheckout', cartItems: $this->cartItems);
        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to initiate checkout: ' . $e->getMessage();
        } finally {
            $this->processingAction = false;
        }
    }

    public function markAsPending()
    {
        if (!$this->hasItems) {
            return;
        }

        $this->processingAction = true;
        $this->errorMessage = null;

        try {
            // Create a pending order in the database
            $orderId = $this->createPendingOrder();

            // Clear the cart after creating the pending order
            $this->dispatch('clearCart');

            // Notify user of success
            $this->dispatch('notify',
                type: 'success',
                message: 'Order marked as pending successfully!'
            );

            // Reset cart data locally after successful order creation
            $this->cartItems = [];
            $this->hasItems = false;
            $this->subtotal = 0;
            $this->total = 0;

        } catch (\Exception $e) {
            // Handle any errors
            $this->errorMessage = 'Failed to save pending order: ' . $e->getMessage();
            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to save pending order: ' . $e->getMessage()
            );
        } finally {
            $this->processingAction = false;
        }
    }

    protected function createPendingOrder()
    {
        // Start a database transaction
        \DB::beginTransaction();

        try {
            // Create a new order
            $order = new \App\Models\Order();
            $order->subtotal = $this->subtotal;
            $order->total = $this->total;
            $order->status = 'pending';
            $order->user_id = auth()->id();
            $order->save();

            // Add items to the order
            foreach ($this->cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'name' => $item['name_en'] ?? ($item['name'] ?? ''),
                    'name_km' => $item['name_km'] ?? null,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'size' => isset($item['size']) ? json_encode($item['size']) : null,
                    'toppings' => isset($item['toppings']) ? json_encode($item['toppings']) : null,
                    'special_instructions' => $item['special_instructions'] ?? null,
                ]);
            }

            // Commit the transaction
            \DB::commit();

            return $order->id;

        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            \DB::rollBack();
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.components.carts.checkout-cart');
    }
}
