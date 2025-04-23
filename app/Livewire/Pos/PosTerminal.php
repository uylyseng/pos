<?php

namespace App\Livewire\Pos;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Url;

class PosTerminal extends Component
{
    public $categories;
    public $products;
    public $cartItems = [];

    #[Url(as: 'cat')]
    public $selectedCategory = null;

    #[Url(as: 'q')]
    public $searchTerm = '';

    public function mount()
    {
        // Only load active categories with active products
        $this->loadCategories();
        $this->loadProducts();

        // Load cart items from session
        $this->cartItems = session('cart', []);
    }

    /**
     * Load active categories
     */
    private function loadCategories()
    {
        $this->categories = Category::where('is_active', true)
            ->with(['products' => function($query) {
                $query->where('is_active', true);
            }])
            ->get();
    }

    /**
     * Load products based on selected category and search term
     */
    public function loadProducts()
    {
        // Start by ensuring we're only querying active products from active categories
        $query = Product::query()
            ->where('products.is_active', true)
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('categories.is_active', true)
            ->select('products.*'); // Select only products fields to avoid column name conflicts

        // Filter by category if one is selected
        if ($this->selectedCategory) {
            // Check if the selected category is still active
            if (!$this->isCategoryActive($this->selectedCategory)) {
                $this->selectedCategory = null;
            } else {
                $query->where('products.category_id', $this->selectedCategory);
            }
        }

        // Filter by search term if provided
        if (!empty($this->searchTerm)) {
            $query->where(function($q) {
                $q->where('products.name_km', 'like', "%{$this->searchTerm}%")
                  ->orWhere('products.name_en', 'like', "%{$this->searchTerm}%");
            });
        }

        $this->products = $query->get();

        // Dispatch products updated event for Alpine.js
        $this->dispatch('products-updated', products: $this->products);
    }

    /**
     * Helper method to check if a category is active
     */
    private function isCategoryActive($categoryId)
    {
        if ($categoryId === null) {
            return true; // "All Products" is always considered active
        }

        return Category::where('id', $categoryId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Set the selected category and reload products
     */
    public function selectCategory($categoryId = null)
    {
        $this->selectedCategory = ($categoryId !== null && $this->isCategoryActive($categoryId))
            ? $categoryId
            : null;

        $this->loadProducts();
    }

    /**
     * Handle search input
     */
    public function updatedSearchTerm()
    {
        $this->loadProducts();
    }

    /**
     * Clear search term
     */
    public function clearSearch()
    {
        $this->searchTerm = '';
        $this->loadProducts();
    }

    /**
     * Reset all filters
     */
    public function resetFilters()
    {
        $this->selectedCategory = null;
        $this->searchTerm = '';
        $this->loadProducts();
    }

    /**
     * Search products with given term
     */
    public function searchProducts($term = null)
    {
        if ($term !== null) {
            $this->searchTerm = $term;
        }
        $this->loadProducts();
    }

    /**
     * Open product modal for customization
     */
    public function openProductModal($productId)
    {
        // Verify the product is active and from an active category before opening
        $product = Product::query()
            ->where('products.id', $productId)
            ->where('products.is_active', true)
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('categories.is_active', true)
            ->select('products.*')
            ->first();

        if ($product) {
            $this->dispatch('openProductModal', productId: $productId);
        }
    }

    #[On('itemAdded')]
    #[On('itemUpdated')]
    #[On('itemRemoved')]
    public function updateCartItems($items)
    {
        $this->cartItems = $items;
        session(['cart' => $items]);
    }

    public function render()
    {
        return view('livewire.pos.index')
            ->layout('layouts.app');
    }
}
