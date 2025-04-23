<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class ReceiptController extends Controller
{
    /**
     * Generate a PDF receipt for the specified order.
     *
     * @param int $orderId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generate($orderId)
    {
        $order = Order::with([
            'items.product',
            'items.size',
            'items.toppings.productTopping.topping',
            'payments.paymentMethod',
            'payments.currency',
            'createdBy'
        ])->findOrFail($orderId);

        // For now, let's just redirect to the order details page
        // In a real implementation, you would generate a PDF here
        return redirect()->route('orders.show', $order->id)
            ->with('message', 'PDF generation will be implemented in the future.');

        // Future implementation would be something like:
        // $pdf = PDF::loadView('receipts.template', compact('order'));
        // $filename = 'order-' . $order->id . '-' . time() . '.pdf';
        // Storage::put('public/receipts/' . $filename, $pdf->output());
        // return redirect()->route('receipts.view', $filename);
    }

    /**
     * View a generated receipt PDF.
     *
     * @param string $filename
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function view($filename)
    {
        if (Storage::exists('public/receipts/' . $filename)) {
            return new Response(
                Storage::get('public/receipts/' . $filename),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $filename . '"'
                ]
            );
        }

        return redirect()->route('orders.index')
            ->with('error', 'Receipt not found.');
    }
}
