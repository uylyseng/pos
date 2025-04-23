<div class="h-full"
     x-data="posApp()"
     x-cloak
     x-init="$nextTick(() => { try { init() } catch(e) { console.error('Initialization error:', e) } })">

    @if(auth()->user()->can('access_pos'))
        <!-- POS interface content -->
        <!-- Desktop layout with grid - Full height -->
        <div class="hidden md:flex h-full">
            <!-- Categories -->
            <div class="w-1/6 bg-white border-r border-gray-300 flex flex-col h-full">
                <div class="p-4 bg-white border-b flex justify-between items-center">
                    <h2 class="text-lg font-semibold">Categories</h2>
                </div>
                <div class="flex-1 overflow-y-auto p-3 space-y-2">
                    <button
                        wire:click="selectCategory(null)"
                        @click="setCategory(null)"
                        class="w-full text-left px-4 py-3 rounded-lg transition flex items-center space-x-2"
                        :class="selectedCategory === null ? 'bg-green-600 text-white' : 'bg-gray-100 hover:bg-gray-200'"
                    >
                        <span>All Products</span>
                    </button>
                    @foreach ($categories as $category)
                        <button
                            wire:click="selectCategory({{ $category->id }})"
                            @click="setCategory({{ $category->id }})"
                            class="w-full text-left px-4 py-3 rounded-lg transition flex items-center space-x-2"
                            :class="selectedCategory === {{ $category->id }} ? 'bg-green-600 text-white' : 'bg-gray-100 hover:bg-gray-200'"
                        >
                            <img src="{{ asset('images/category.png') }}" class="w-8 h-8 object-cover rounded">
                            <div>
                                <div class="truncate">{{ $category->name_km }}</div>
                                <div class="text-xs truncate opacity-75">{{ $category->name_en }}</div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Products -->
            <div class="w-3/5 flex flex-col h-full bg-gray-100">
                <!-- Search bar and other content remains the same -->
                <div class="p-2 bg-white border-b flex justify-between items-center">
                    <h2 class="text-lg font-semibold">Products</h2>
                    <div class="relative w-128">
                        <input
                            type="text"
                            placeholder="Search products..."
                            class="w-full rounded-lg border-gray-300 border-2 pl-10 pr-4 py-2 focus:ring-green-600 focus:border-green-500 shadow-sm transition duration-200 ease-in-out hover:border-green-300"
                            wire:model.debounce.300ms="searchTerm"
                            x-model="searchTerm"
                            @input="searchProducts"
                        >
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-green-500"
                             fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <button
                            x-show="searchTerm.length > 0"
                            @click="searchTerm = ''; searchProducts()"
                            wire:click="clearSearch"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-green-600"
                            type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Loading Spinner - Only show for major data operations -->
                <div wire:loading.delay wire:target="loadProducts, selectCategory, resetFilters, searchTerm"
                     class="absolute inset-0 z-10 bg-white bg-opacity-75 flex justify-center items-center">
                    <div class="flex flex-col items-center">
                        <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-green-600 mb-2"></div>
                        <p class="text-green-600 font-medium">Loading...</p>
                    </div>
                </div>

                <!-- Products Grid - Use flex-1 not wire:loading.remove -->
                <div class="flex-1 overflow-y-auto p-4 relative">
                    @if(count($products) === 0)
                        <div class="flex h-full items-center justify-center">
                            <div class="text-center text-green-600 bg-white p-8 rounded-lg shadow-md border border-green-200 max-w-md transition-all hover:shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-20 w-20 mx-auto text-green-500 mb-4 opacity-75"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M10 21h4m-2-5v-2m0-10L4.6 8.7a2 2 0 001.5 3.3h11.8a2 2 0 001.5-3.3L13 4m-1 8l-2 2m4 0l-2-2"/>
                                </svg>
                                <p class="text-xl font-semibold mb-2">No products found</p>
                                <p class="text-gray-500 mb-4">Try adjusting your search or selecting a different category</p>
                                <button wire:click="resetFilters"
                                        @click="searchTerm = ''; setCategory(null)"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                                    Show all products
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
                            @foreach($products as $product)
                                <div class="bg-white rounded-lg shadow-sm overflow-hidden cursor-pointer hover:shadow-md transition border border-green-300"
                                     @click.prevent="openProductCustomization({{ $product->id }})"
                                     wire:key="product-{{ $product->id }}">
                                    <div class="aspect-w-1 aspect-h-1">
                                        <img src="{{ $product->image ? asset('storage/' . str_replace('public/', '', $product->image)) : asset('images/default.jpg') }}"
                                             class="object-cover w-full h-40"
                                             alt="{{ $product->name_en }}">
                                    </div>
                                    <div class="p-4">
                                        <div class="font-medium truncate">{{ $product->name_km }}</div>
                                        <div class="text-sm text-gray-500 truncate">{{ $product->name_en }}</div>
                                        <div class="flex justify-between items-center mt-2">
                                            <span class="text-green-600 font-bold text-lg">${{ number_format($product->base_price, 2) }}</span>
                                            <button
                                                class="bg-green-100 text-green-600 p-1 rounded-full hover:bg-green-200 transform transition-transform duration-200 hover:scale-110"
                                                wire:click.stop="openProductModal({{ $product->id }})"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <!-- Indicators for size and topping availability -->
                                        <div class="flex space-x-2 mt-2">
                                            @if($product->has_sizes)
                                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full">
                                                    Sizes
                                                </span>
                                            @endif
                                            @if($product->has_toppings)
                                                <span class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full">
                                                    Toppings
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cart -->
            <div class="w-1/3 bg-white border-l flex flex-col h-full">
                <div class="p-4 bg-white border-b flex justify-between items-center">
                    <h2 class="text-lg font-semibold">Current Order</h2>
                </div>

                <!-- Cart Items -->
                <livewire:components.carts.item-cart />
                <!-- Cart Summary -->
                <livewire:components.carts.checkout-cart />
            </div>
        </div>

        <!-- Modals -->
        <livewire:components.modals.customization-modal />
        <livewire:components.modals.checkout-modal />
        <livewire:components.modals.completed-modal />

    @else
        <div class="flex items-center justify-center h-screen">
            <div class="text-center p-6 bg-white rounded-lg shadow-lg">
                <h1 class="text-2xl font-bold text-red-600 mb-4">Access Denied</h1>
                <p class="mb-4">You do not have permission to access the POS system.</p>
                <a href="{{ route('dashboard') }}"
                   class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Back to Dashboard
                </a>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function posApp() {
        return {
            selectedCategory: null,
            searchTerm: '',

            init() {
                // Use $wire to reference the Livewire component
                this.selectedCategory = this.$wire.selectedCategory;
                this.searchTerm = this.$wire.searchTerm;

                // Listen for updated product list from Livewire
                document.addEventListener('livewire:initialized', () => {
                    Livewire.on('products-updated', ({ products }) => {
                        console.log('Products updated:', products.length);
                    });

                    Livewire.on('resetPos', () => {
                        this.resetFilters();
                    });

                    // Listen for cart events
                    Livewire.on('cart-updated', ({ cart }) => {
                        console.log('Cart updated:', cart.length);
                    });
                });
            },

            setCategory(categoryId) {
                // Let Livewire handle the category validation
                this.selectedCategory = categoryId;
                this.$wire.selectCategory(categoryId);
            },

            openProductCustomization(productId) {
                // Prevent default browser navigation
                event.preventDefault();
                this.$wire.openProductModal(productId);
                return false; // Additional safety
            },

            getImageUrl(imagePath) {
                if (!imagePath) return 'images/default.jpg';
                return imagePath.startsWith('http') ? imagePath : '/storage/' + imagePath.replace('public/', '');
            },

            resetFilters() {
                this.searchTerm = '';
                this.selectedCategory = null;
                this.$wire.resetFilters();
            },

            searchProducts() {
                this.$wire.searchTerm = this.searchTerm;
                this.$wire.loadProducts();
            }
        };
    }
</script>
@endpush
