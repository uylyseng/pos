<?php

namespace App\Livewire\Components\Carts;

use Livewire\Component;
use Livewire\Attributes\On;

class ItemCart extends Component
{
    public $items = [];
    public $hasItems = false;

    public function mount()
    {
        // Get cart from session when component loads
        $this->items = session('cart', []);
        $this->hasItems = count($this->items) > 0;
    }

    #[On('cartUpdated')]
    public function handleCartUpdated($items = null)
    {
        if ($items !== null) {
            $this->items = $items;
            $this->hasItems = count($this->items) > 0;
        }
    }

    #[On('addToCart')]
    public function addToCart($item)
    {
        // Add new item to cart
        $this->items[] = $item;
        $this->hasItems = true;
        $this->saveCart();

        // Notify other components
        $this->dispatch('itemAdded', items: $this->items);
    }

    #[On('updateCartItem')]
    public function updateCartItem($item, $index)
    {
        if (isset($this->items[$index])) {
            $this->items[$index] = $item;
            $this->saveCart();
            $this->dispatch('itemUpdated', items: $this->items);
        }
    }

    #[On('refreshCart')]
    public function updateCart($cartItems = null)
    {
        if ($cartItems !== null) {
            $this->items = $cartItems;
        } else {
            // Get cart from session
            $this->items = session('cart', []);
        }

        $this->hasItems = count($this->items) > 0;

        // Dispatch updated cart data to other components
        $this->dispatch('cartUpdated', items: $this->items);
    }

    #[On('clearCart')]
    public function clearItems()
    {
        $this->items = [];
        $this->hasItems = false;
        $this->saveCart();

        // Dispatch cleared cart
        $this->dispatch('cartUpdated', items: $this->items);
    }

    public function clearCart()
    {
        // Show confirmation dialog before clearing
        $this->dispatch('showConfirmation',
            title: 'Clear Cart',
            message: 'Are you sure you want to clear all items from your cart?',
            confirmMethod: 'confirmedClearCart'
        );
    }

    public function confirmedClearCart()
    {
        $this->items = [];
        $this->hasItems = false;
        $this->saveCart();

        // Dispatch both events to ensure all components update
        $this->dispatch('cartCleared', items: $this->items);
        $this->dispatch('cartUpdated', items: $this->items);

        // Show success message
        $this->dispatch('notify',
            type: 'success',
            message: 'Cart cleared successfully!'
        );
    }

    public function increaseQuantity($index)
    {
        if (!isset($this->items[$index])) {
            return;
        }

        $this->items[$index]['quantity']++;
        $this->updateItemTotal($index);
        $this->saveCart();

        $this->dispatch('itemUpdated', items: $this->items);
    }

    public function decreaseQuantity($index)
    {
        if (!isset($this->items[$index]) || $this->items[$index]['quantity'] <= 1) {
            return;
        }

        $this->items[$index]['quantity']--;
        $this->updateItemTotal($index);
        $this->saveCart();

        $this->dispatch('itemUpdated', items: $this->items);
    }

    public function updateItemTotal($index)
    {
        if (!isset($this->items[$index])) {
            return;
        }

        $item = $this->items[$index];
        $this->items[$index]['total'] = $item['price'] * $item['quantity'];
    }

    public function removeItem($index)
    {
        if (!isset($this->items[$index])) {
            return;
        }

        // Remove the item
        array_splice($this->items, $index, 1);
        $this->hasItems = count($this->items) > 0;
        $this->saveCart();

        $this->dispatch('itemRemoved', items: $this->items);
    }

    public function editItem($index)
    {
        if (!isset($this->items[$index])) {
            return;
        }

        $item = $this->items[$index];

        // If the item has a product_id, we can edit it by opening the customization modal
        if (isset($item['product_id'])) {
            $this->dispatch('editCartItem', item: $item, index: $index);
        }
    }

    protected function saveCart()
    {
        // Save to session
        session(['cart' => $this->items]);

        // Update hasItems flag
        $this->hasItems = count($this->items) > 0;

        // Dispatch browser event for Alpine.js to update
        $this->dispatch('cart-updated', cart: $this->items);
    }

    public function render()
    {
        return view('livewire.components.carts.item-cart');
    }
}
