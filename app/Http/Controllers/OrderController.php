<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * Display the specified order.
     *
     * @param Order $order
     * @return View
     */
    public function show(Order $order): View
    {
        // Eager load relationships to avoid N+1 queries
        $order->load(['items.product', 'user', 'payments', 'createdBy']);

        return view('livewire.order-history.show', compact('order'));
    }

    /**
     * Generate and display a receipt for the specified order.
     *
     * @param Order $order
     * @return Response
     */
    public function receipt(Order $order)
    {
        // Redirect to the receipt generation controller
        return redirect()->route('receipts.generate', $order->id);
    }
}
