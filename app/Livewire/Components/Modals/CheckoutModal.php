<?php

namespace App\Livewire\Components\Modals;

use Livewire\Component;
use App\Models\Order;
use App\Models\Payment; // Add this import
use App\Models\PaymentMethod;
use App\Models\ExchangeRate;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use App\Services\ExchangeRateService;

class CheckoutModal extends Component
{
    public $showModal = false;
    public $cartItems = [];
    public $subtotal = 0;
    public $total = 0;
    public $discountAmount = 0;
    public $paymentMethod = 'cash';
    public $paymentMethods = [];

    // Table information
    public $tableNumber = '';
    public $availableTables = [];
    public $isTableRequired = false;

    // User tracking
    public $currentUser = null;

    // Cash payment specific properties
    public $cashAmount = 0;
    public $cashAmountRiel = 0;
    public $exchangeRate = 4100; // Default exchange rate
    public $changeUSD = 0;
    public $changeKHR = 0;

    public $canCompleteOrder = false;

    // Currency IDs
    protected $usdCurrencyId;
    protected $khrCurrencyId;

    /**
     * Flag to determine if this is for a pending order
     */
    public $isPendingCheckout = false;

    public function mount()
    {
        $this->loadPaymentMethods();
        $this->loadAvailableTables();
        $this->currentUser = auth()->user();
        // Check if table is required from settings
        $this->isTableRequired = config('app.require_table_number', false);

        // Load currency IDs and exchange rate
        $this->loadCurrencyData();

        // Create the service directly
        $exchangeService = new ExchangeRateService();
        $this->exchangeRate = $exchangeService->getRate();
    }

    /**
     * Load currency IDs and current exchange rate from database
     */
    private function loadCurrencyData()
    {
        // Get currency IDs by code
        $usdCurrency = Currency::where('code', 'USD')->first();
        $khrCurrency = Currency::where('code', 'KHR')->first();

        if ($usdCurrency && $khrCurrency) {
            $this->usdCurrencyId = $usdCurrency->id;
            $this->khrCurrencyId = $khrCurrency->id;

            // Get current exchange rate
            $currentRate = ExchangeRate::getCurrentRate($this->usdCurrencyId, $this->khrCurrencyId);

            if ($currentRate) {
                $this->exchangeRate = floatval($currentRate->rate);
            }
        }
    }

    /**
     * Load active payment methods from the database
     */
    public function loadPaymentMethods()
    {
        $this->paymentMethods = PaymentMethod::active()
                                            ->orderByName()
                                            ->get();

        if ($this->paymentMethods->count() > 0) {
            $this->paymentMethod = strtolower($this->paymentMethods->first()->name_en);
        }
    }

    /**
     * Load available tables for selection
     */
    private function loadAvailableTables()
    {
        // This method is no longer needed as we're using a text input
        // but keeping it to maintain compatibility with existing code
        $this->availableTables = [];
    }

    /**
     * Open the checkout modal and initialize with cart items
     */
    #[On('initiateCheckout')]
    public function openCheckout($cartItems, $tableNumber = null)
    {
        $this->reset(['cashAmount', 'cashAmountRiel', 'changeUSD', 'changeKHR']);

        // Make sure we have the latest exchange rate
        $this->loadCurrencyData();

        $this->cartItems = array_map(function($item) {
            return array_merge($item, [
                'name_en' => $item['name_en'] ?? '',
                'image_url' => $item['image_url'] ?? null,
                'toppings' => array_map(function($topping) {
                    return array_merge($topping, [
                        'name_en' => $topping['name_en'] ?? ''
                    ]);
                }, $item['toppings'] ?? [])
            ]);
        }, $cartItems);
        $this->calculateTotals();

        // Set table number if provided
        if ($tableNumber) {
            $this->tableNumber = $tableNumber;
        }

        // Initialize with appropriate values based on payment method
        $this->initializePaymentDefaults();

        $this->showModal = true;
    }

    /**
     * Open the checkout modal for a pending order
     */
    #[On('initiateCheckoutForPending')]
    public function openCheckoutForPending($cartItems, $tableNumber = null)
    {
        $this->isPendingCheckout = true;
        $this->openCheckout($cartItems, $tableNumber);
    }

    /**
     * Initialize default values based on payment method
     */
    private function initializePaymentDefaults()
    {
        if ($this->paymentMethod === 'cash') {
            $this->cashAmount = $this->total;
            $this->updateChange();
        } else {
            $this->canCompleteOrder = true;
        }
    }

    /**
     * Close the checkout modal
     */
    public function closeModal()
    {
        $this->showModal = false;
    }

    /**
     * Calculate subtotal, discounts, and total from cart items
     */
    public function calculateTotals()
    {
        $this->subtotal = array_reduce($this->cartItems, function($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        // Calculate any discounts here if needed
        $this->discountAmount = 0;

        $this->total = $this->subtotal - $this->discountAmount;
    }

    /**
     * Handle payment method change
     */
    public function updatedPaymentMethod()
    {
        $this->initializePaymentDefaults();
    }

    /**
     * Set cash amount in USD
     */
    public function setCashAmount($amount)
    {
        $this->updateCashValues($amount, 0);
    }

    /**
     * Set cash amount in Riel
     */
    public function setCashAmountRiel($amount)
    {
        $this->updateCashValues(0, $amount);
    }

    /**
     * Handle cash amount update
     */
    public function updateCashAmount()
    {
        $this->updateCashValues($this->cashAmount, 0);
    }

    /**
     * Handle cash amount in riel update
     */
    public function updateCashAmountRiel()
    {
        $this->updateCashValues(0, $this->cashAmountRiel);
    }

    /**
     * Update cash values and recalculate change
     */
    private function updateCashValues($usdAmount, $rielAmount)
    {
        $this->cashAmount = $usdAmount;
        $this->cashAmountRiel = $rielAmount;
        $this->updateChange();
    }

    /**
     * Calculate change based on tendered amount
     */
    public function updateChange()
    {
        $exchangeRateService = app(ExchangeRateService::class);

        // Calculate tendered amount in USD
        if ($this->cashAmount > 0) {
            $tendered = $this->cashAmount;
        } elseif ($this->cashAmountRiel > 0) {
            // Convert KHR to USD
            $tendered = $exchangeRateService->khrToUsd($this->cashAmountRiel);
        } else {
            $tendered = 0;
        }

        // Calculate change in USD
        $this->changeUSD = max(0, $tendered - $this->total);

        // Convert change to KHR
        $this->changeKHR = $exchangeRateService->usdToKhr($this->changeUSD);

        $this->checkIfCanComplete();
    }

    /**
     * Calculate the tendered amount in USD
     */
    private function calculateTenderedAmount()
    {
        if ($this->cashAmount > 0) {
            return $this->cashAmount;
        } elseif ($this->cashAmountRiel > 0) {
            return $this->cashAmountRiel / $this->exchangeRate;
        }
        return 0;
    }

    /**
     * Check if the order can be completed
     */
    public function checkIfCanComplete()
    {
        if ($this->paymentMethod === 'cash') {
            $tendered = $this->calculateTenderedAmount();
            $hasEnoughMoney = $tendered >= $this->total;
        } else {
            $hasEnoughMoney = true;
        }

        // Check if table is required but not provided
        $tableIsValid = !$this->isTableRequired || ($this->tableNumber != '');

        // Additional validation for table number range
        if ($this->tableNumber != '') {
            $tableNum = (int) $this->tableNumber;
            if ($tableNum < 1 || $tableNum > 99) {
                $tableIsValid = false;
                $this->addError('table', 'Table number must be between 1 and 99');
            }
        }

        $this->canCompleteOrder = $hasEnoughMoney && $tableIsValid;

        if ($this->isTableRequired && $this->tableNumber == '') {
            $this->addError('table', 'Please enter a table number');
        }
    }

    /**
     * Complete the order
     */
    public function completeOrder()
    {
        if (!$this->canCompleteOrder) {
            // Add validation errors if needed
            return;
        }

        try {
            DB::beginTransaction();

            // Create the order
            $order = $this->createOrderRecord();

            // Add order items (your existing code)
            $this->addOrderItems($order);

            DB::commit();

            // Dispatch events and close modal
            $this->dispatch('orderCompleted', orderId: $order->id);
            $this->dispatch('clearCart');
            $this->closeModal();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('order', 'Failed to process order: ' . $e->getMessage());
            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to process order: ' . $e->getMessage()
            );
        }
    }

    /**
     * Create the order record
     */
    private function createOrderRecord()
    {
        $order = new Order();
        $order->subtotal = $this->subtotal;
        $order->discount_amount = $this->discountAmount;
        $order->total = $this->total;
        $order->exchange_rate = $this->exchangeRate;

        // Add currency information if available
        if ($this->usdCurrencyId && $this->khrCurrencyId) {
            $order->from_currency_id = $this->usdCurrencyId;
            $order->to_currency_id = $this->khrCurrencyId;
        }

        // Add table number if provided
        if ($this->tableNumber) {
            $order->table_number = $this->tableNumber;
        }

        // Track user who created the order
        $order->user_id = auth()->id();
        $order->created_by = auth()->id();
        $order->save();

        // Create a payment record to store the payment method and cash details
        $this->createPaymentRecord($order);

        return $order;
    }

    /**
     * Create a payment record for the order
     */
    private function createPaymentRecord($order)
    {
        // Find the payment method ID based on the selected method name
        $paymentMethodId = null;
        foreach ($this->paymentMethods as $method) {
            if (strtolower($method->name_en) === $this->paymentMethod) {
                $paymentMethodId = $method->id;
                break;
            }
        }

        if ($paymentMethodId) {
            $payment = $order->payments()->create([
                'payment_method_id' => $paymentMethodId,
                'amount' => $this->total,
                'amount_in_default_currency' => $this->total,
                'exchange_rate' => $this->exchangeRate,
                'status' => 'completed',
                'created_by' => auth()->id(),
            ]);

            // Add cash payment details as note or in metadata if needed
            if ($this->paymentMethod === 'cash') {
                // Note: Since there's no cash_tendered fields in the orders table,
                // we might handle it in a different way if needed
            }
        }
    }

    /**
     * Add order items from cart
     */
    private function addOrderItems($order)
    {
        foreach ($this->cartItems as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'name' => $item['name_en'] ?? null,
                'name_km' => $item['name_km'] ?? null,
                'unit_price' => $item['price'], // Use 'unit_price' instead of 'price'
                'price' => $item['price'], // Keep 'price' if your model needs it
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
                'size' => isset($item['size']) ? json_encode($item['size']) : null,
                'toppings' => isset($item['toppings']) ? json_encode($item['toppings']) : null,
                'special_instructions' => $item['special_instructions'] ?? null,
                'image_url' => $item['image_url'] ?? null,
                'created_by' => auth()->id(), // Add created_by since it's in fillable
            ]);
        }
    }

    /**
     * Set table number
     */
    public function setTableNumber($number)
    {
        $this->tableNumber = $number;
        $this->checkIfCanComplete();
    }

    /**
     * Handle table number update
     */
    public function updatedTableNumber()
    {
        // Validate table number is between 1 and 99
        if (!empty($this->tableNumber)) {
            $tableNum = (int) $this->tableNumber;

            if ($tableNum < 1 || $tableNum > 99) {
                $this->addError('table', 'Table number must be between 1 and 99');
                $this->canCompleteOrder = false;
                return;
            }

            // Cast to integer to remove leading zeros
            $this->tableNumber = $tableNum;
        }

        // Clear error if table number is now provided
        if ($this->tableNumber != '') {
            $this->resetErrorBag('table');
        }

        $this->checkIfCanComplete();
    }

    /**
     * Mark the order as pending
     */
    public function markAsPending()
    {
        // Basic validation
        if (empty($this->cartItems)) {
            $this->addError('order', 'Cannot create order with pending payment. Cart is empty.');
            return;
        }
        
        try {
            DB::beginTransaction();

            // Create the order record - without setting status field on Order
            $order = new Order();
            $order->subtotal = $this->subtotal;
            $order->discount_amount = $this->discountAmount;
            $order->total = $this->total;
            $order->exchange_rate = $this->exchangeRate;
            
            // Add currency information if available
            if ($this->usdCurrencyId && $this->khrCurrencyId) {
                $order->from_currency_id = $this->usdCurrencyId;
                $order->to_currency_id = $this->khrCurrencyId;
            }

            // Add table number if provided
            if ($this->tableNumber) {
                $order->table_number = $this->tableNumber;
            }

            // Track user who created the order
            $order->user_id = auth()->id();
            $order->created_by = auth()->id();
            $order->save();

            // Add order items
            $this->addOrderItems($order);
            
            // Create a pending payment record - this is the key part
            $this->createPendingPaymentRecord($order);

            DB::commit();

            // Dispatch events and close modal
            $this->dispatch('orderMarkedAsPending', orderId: $order->id);
            $this->dispatch('clearCart');
            $this->dispatch('notify', 
                type: 'success', 
                message: 'Order created with pending payment successfully!'
            );
            $this->closeModal();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('order', 'Failed to create order with pending payment: ' . $e->getMessage());
            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to create order with pending payment: ' . $e->getMessage()
            );
        }
    }

    /**
     * Create a pending payment record for the order
     */
    private function createPendingPaymentRecord($order)
    {
        // Find the payment method ID based on the selected method name
        $paymentMethodId = null;
        foreach ($this->paymentMethods as $method) {
            if (strtolower($method->name_en) === $this->paymentMethod) {
                $paymentMethodId = $method->id;
                break;
            }
        }

        if ($paymentMethodId) {
            $order->payments()->create([
                'payment_method_id' => $paymentMethodId,
                'amount' => $this->total,
                'amount_in_default_currency' => $this->total,
                'exchange_rate' => $this->exchangeRate,
                'status' => Payment::STATUS_PENDING, // Use the constant
                'created_by' => auth()->id(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.components.modals.checkout-modal');
    }
}
