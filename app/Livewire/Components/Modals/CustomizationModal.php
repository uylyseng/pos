<?php

namespace App\Livewire\Components\Modals;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\ProductTopping;
use Livewire\Attributes\On;

class CustomizationModal extends Component
{
    public $showModal = false;
    public $product = null;
    public $productId = null;
    public $sizes = [];
    public $toppings = [];
    public $selectedSizeIndex = 0;
    public $selectedToppings = [];
    public $quantity = 1;
    public $specialInstructions = '';
    public $editMode = false;
    public $editItemIndex = null;

    #[On('openProductModal')]
    public function openModal($productId)
    {
        $this->reset(['sizes', 'toppings', 'selectedSizeIndex', 'selectedToppings', 'quantity', 'specialInstructions', 'editMode', 'editItemIndex']);

        $this->productId = $productId;
        $this->product = Product::findOrFail($productId);
        $this->loadProductOptions();

        $this->quantity = 1;
        $this->specialInstructions = '';

        $this->showModal = true;
    }

    #[On('editCartItem')]
    public function editCartItem($item, $index)
    {
        $this->reset(['sizes', 'toppings', 'selectedSizeIndex', 'selectedToppings', 'quantity', 'specialInstructions']);

        // Set edit mode
        $this->editMode = true;
        $this->editItemIndex = $index;

        // Load product
        $this->productId = $item['product_id'];
        $this->product = Product::findOrFail($item['product_id']);

        // Load options
        $this->loadProductOptions();

        // Set quantity and special instructions
        $this->quantity = $item['quantity'];
        $this->specialInstructions = $item['special_instructions'] ?? '';

        // Set selected size if exists
        if (!empty($item['size'])) {
            foreach ($this->sizes as $idx => $size) {
                if ($size['id'] == $item['size']['id']) {
                    $this->selectedSizeIndex = $idx;
                    break;
                }
            }
        }

        // Set selected toppings if exists
        if (!empty($item['toppings'])) {
            foreach ($item['toppings'] as $itemTopping) {
                foreach ($this->toppings as $idx => $topping) {
                    if ($topping['id'] == $itemTopping['id']) {
                        $this->selectedToppings[$idx] = true;
                    }
                }
            }
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function loadProductOptions()
    {
        // Load sizes if product has them
        if ($this->product->has_sizes) {
            // Use Eloquent relationships instead of joins
            $this->sizes = ProductSize::where('product_id', $this->product->id)
                ->with('size')  // Eager load the size relationship
                ->orderBy('multiplier')
                ->get()
                ->map(function ($productSize) {
                    // Create a consistent structure
                    return [
                        'id' => $productSize->id,
                        'multiplier' => $productSize->multiplier,
                        'name_km' => $productSize->size->name_km ?? '',
                        'name_en' => $productSize->size->name_en ?? '',
                    ];
                })
                ->toArray();
        }

        // Load toppings if product has them - similar approach
        if ($this->product->has_toppings) {
            $this->toppings = ProductTopping::where('product_id', $this->product->id)
                ->with('topping')  // Eager load the topping relationship
                ->orderBy(function($query) {
                    $query->select('toppings.name_km')
                        ->from('toppings')
                        ->whereColumn('toppings.id', 'product_toppings.topping_id')
                        ->limit(1);
                })
                ->get()
                ->map(function ($productTopping) {
                    return [
                        'id' => $productTopping->id,
                        'price' => $productTopping->price,
                        'name_km' => $productTopping->topping->name_km ?? '',
                        'name_en' => $productTopping->topping->name_en ?? '',
                    ];
                })
                ->toArray();

            $this->selectedToppings = array_fill(0, count($this->toppings), false);
        }
    }

    public function selectSize($index)
    {
        $this->selectedSizeIndex = $index;
    }

    public function toggleTopping($index)
    {
        $this->selectedToppings[$index] = !($this->selectedToppings[$index] ?? false);
    }

    public function increaseQuantity()
    {
        $this->quantity++;
    }

    public function decreaseQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function getBasePrice()
    {
        if (!$this->product) {
            return 0;
        }

        $basePrice = $this->product->base_price;

        if (count($this->sizes) > 0 && isset($this->sizes[$this->selectedSizeIndex])) {
            $basePrice = $this->product->base_price * $this->sizes[$this->selectedSizeIndex]['multiplier'];
        }

        return $basePrice;
    }

    public function getToppingsCost()
    {
        $toppingsCost = 0;

        for ($i = 0; $i < count($this->toppings); $i++) {
            if (isset($this->selectedToppings[$i]) && $this->selectedToppings[$i]) {
                $toppingsCost += $this->toppings[$i]['price'];
            }
        }

        return $toppingsCost;
    }

    public function getCurrentPrice()
    {
        return $this->getBasePrice() + $this->getToppingsCost();
    }

    public function getSelectedToppings()
    {
        $selectedToppingItems = [];

        for ($i = 0; $i < count($this->toppings); $i++) {
            if (isset($this->selectedToppings[$i]) && $this->selectedToppings[$i]) {
                // Already normalized - just use directly
                $selectedToppingItems[] = $this->toppings[$i];
            }
        }

        return $selectedToppingItems;
    }

    public function addToCart()
    {
        if (!$this->product) {
            return;
        }

        // Create cart item using the helper method
        $cartItem = $this->createCartItem();

        // Dispatch appropriate event based on mode
        if ($this->editMode) {
            $this->dispatch('updateCartItem', item: $cartItem, index: $this->editItemIndex);
        } else {
            $this->dispatch('addToCart', item: $cartItem);
        }

        $this->closeModal();
    }

    private function createCartItem()
    {
        $selectedSize = null;
        if (count($this->sizes) > 0) {
            // No need for complicated normalization since our data is already consistent
            $selectedSize = $this->sizes[$this->selectedSizeIndex];
        }

        // Rest remains the same
        return [
            'id' => $this->editMode ? null : uniqid('item_'),
            'product_id' => $this->product->id,
            'name_km' => $this->product->name_km,
            'name_en' => $this->product->name_en,
            'image' => $this->product->image,
            'base_price' => $this->getBasePrice(),
            'price' => $this->getCurrentPrice(),
            'quantity' => $this->quantity,
            'size' => $selectedSize,
            'toppings' => $this->getSelectedToppings(),
            'special_instructions' => $this->specialInstructions,
            'total' => $this->getCurrentPrice() * $this->quantity
        ];
    }

    public function render()
    {
        return view('livewire.components.modals.customization-modal');
    }
}
