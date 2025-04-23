<?php

namespace App\Livewire\Components\Modals;

use Livewire\Component;
use App\Models\Order;
use Livewire\Attributes\On;

class CompletedModal extends Component
{
    public $showModal = false;
    public $orderId = null;
    public $orderTotal = 0;
    public $paymentMethod = null;
    public $cashAmountUsd = 0;
    public $cashAmountRiel = 0;
    public $changeUsd = 0;
    public $changeKhr = 0;
    public $exchangeRate = 4100; // Default exchange rate USD to KHR
    public $userName = null; // Added property for user name

    #[On('orderCompleted')]
    public function showCompletedOrder($orderId)
    {
        $this->resetExcept('exchangeRate');

        $order = Order::with('user')->findOrFail($orderId);

        $this->orderId = $order->id;
        $this->orderTotal = $order->total;
        $this->paymentMethod = $order->payment_method;
        $this->userName = $order->user ? $order->user->name : 'Guest'; // Get user name from the order

        if ($this->paymentMethod === 'cash') {
            $this->cashAmountUsd = $order->cash_tendered;
            $this->cashAmountRiel = $order->cash_tendered_riel;
            $this->changeUsd = $order->change_amount;
            $this->changeKhr = $order->change_amount_riel;
        }

        $this->showModal = true;
    }

    #[On('updateExchangeRate')]
    public function setExchangeRate($rate)
    {
        $this->exchangeRate = $rate;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function printReceipt()
    {
        // Dispatch event to print receipt
        $this->dispatch('printReceipt', orderId: $this->orderId);

        // Open print dialog using JavaScript
        $this->dispatch('print-receipt', [
            'orderId' => $this->orderId
        ]);
    }

    public function newOrder()
    {
        // Close the modal
        $this->closeModal();

        // Reset the POS system for a new order
        $this->dispatch('resetPos');
    }

    public function render()
    {
        return view('livewire.components.modals.completed-modal');
    }
}
