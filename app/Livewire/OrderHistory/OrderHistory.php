<?php

namespace App\Livewire\OrderHistory;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class OrderHistory extends Component
{
    use WithPagination;

    public $status = 'all';
    public $search = '';

    protected $queryString = [
        'status' => ['except' => 'all'],
        'search' => ['except' => '']
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getOrdersCountProperty(): array
    {
        return [
            'total' => Order::count(),
            'completed' => Order::whereHas('payments', function($query) {
                $query->where('status', 'completed');
            })->count(),
            'pending' => Order::whereHas('payments', function($query) {
                $query->where('status', 'pending');
            })->count(),
            'failed' => Order::whereHas('payments', function($query) {
                $query->where('status', 'failed');
            })->count(),
        ];
    }

    public function getOrdersProperty()
    {
        $query = Order::with(['items.product', 'payments', 'user', 'createdBy'])
            ->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('id', 'like', "%{$this->search}%")
                    ->orWhereHas('user', function($query) {
                        $query->where('name', 'like', "%{$this->search}%");
                    });
            });
        }

        if ($this->status !== 'all') {
            $query->whereHas('payments', function($q) {
                $q->where('status', $this->status);
            });
        }

        return $query->paginate(10);
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.order-history.order-history', [
            'orders' => $this->orders,
            'ordersCount' => $this->ordersCount
        ]);
    }
}
