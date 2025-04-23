<?php
//
//namespace App\Http\Livewire\Components\Carts;
//
//use Livewire\Component;
//
//class ItemCart extends Component
//{
//    public $items = [];
//    public $hasItems = false;
//
//    protected $listeners = [
//        'cartUpdated' => 'updateCart',
//        'itemAdded' => 'updateCart',
//        'refreshCart' => 'updateCart',
//        'clearCart' => 'clearItems',
//    ];
//
//    public function mount()
//    {
//        $this->updateCart();
//    }
//
//    public function updateCart($cartItems = null)
//    {
//        if ($cartItems !== null) {
//            $this->items = $cartItems;
//        } else {
//            // Get cart from session or local storage
//            $this->items = session('cart', []);
//        }
//
//        $this->hasItems = count($this->items) > 0;
//
//        // Emit event to update checkout component with the same cart data
//        $this->emit('cartUpdated', $this->items);
//    }
//
//    public function increaseQuantity($index)
//    {
//        if (!isset($this->items[$index])) {
//            return;
//        }
//
//        $this->items[$index]['quantity']++;
//        $this->updateItemTotal($index);
//        $this->saveCart();
//
//        $this->emit('itemUpdated', $this->items);
//    }
//
//    public function decreaseQuantity($index)
//    {
//        if (!isset($this->items[$index]) || $this->items[$index]['quantity'] <= 1) {
//            return;
//        }
//
//        $this->items[$index]['quantity']--;
//        $this->updateItemTotal($index);
//        $this->saveCart();
//
//        $this->emit('itemUpdated', $this->items);
//    }
//
//    public function updateItemTotal($index)
//    {
//        if (!isset($this->items[$index])) {
//            return;
//        }
//
//        $item = $this->items[$index];
//        $this->items[$index]['total'] = $item['price'] * $item['quantity'];
//    }
//
//    public function removeItem($index)
//    {
//        if (!isset($this->items[$index])) {
//            return;
//        }
//
//        // Remove the item
//        array_splice($this->items, $index, 1);
//        $this->hasItems = count($this->items) > 0;
//        $this->saveCart();
//
//        $this->emit('itemRemoved', $this->items);
//    }
//
//    public function editItem($index)
//    {
//        if (!isset($this->items[$index])) {
//            return;
//        }
//
//        $item = $this->items[$index];
//
//        // If the item has a product_id, we can edit it by opening the customization modal
//        if (isset($item['product_id'])) {
//            $this->emit('editCartItem', $item, $index);
//        }
//    }
//
//    public function clearItems()
//    {
//        $this->items = [];
//        $this->hasItems = false;
//        $this->saveCart();
//
//        $this->emit('cartUpdated', $this->items);
//    }
//
//    protected function saveCart()
//    {
//        // Save to session
//        session(['cart' => $this->items]);
//
//        // Also emit an event to update any Alpine.js state
//        $this->dispatchBrowserEvent('cart-updated', [
//            'cart' => $this->items
//        ]);
//    }
//
//    public function render()
//    {
//        return view('livewire.components.carts.item-cart');
//    }
//}
