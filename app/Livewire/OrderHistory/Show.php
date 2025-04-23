<?php

namespace App\Livewire\OrderHistory;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Show extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        // Eager load relationships to avoid N+1 queries
        $this->order = $order->load([
            'items.product',
            'items.size',
            'items.toppings.productTopping.topping',
            'user',
            'payments.paymentMethod',
            'payments.currency',
            'createdBy',
            'discount'
        ]);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.order-history.show');
    }
}
