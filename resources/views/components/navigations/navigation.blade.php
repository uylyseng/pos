<nav x-data="{ open: false }" class="bg-green-600 text-white sticky top-0 z-50 shadow-md">
    <div class="w-full px-2 sm:px-4">
        <div class="flex justify-between h-14 sm:h-16">
            <div class="flex items-center">
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/pos') }}" class="text-base sm:text-lg font-bold truncate max-w-[120px] sm:max-w-[180px] flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                        </svg>
                        <span>សួស្តីកាហ្វេ នំខេកនិងភីហ្សា</span>
                    </a>
                </div>

                <div class="hidden space-x-4 sm:space-x-6 sm:ml-4 sm:flex">
                    <a href="{{ url('/pos') }}"
                       class="{{ request()->is('pos') && !request()->is('pos/orders') ? 'bg-green-700/40' : '' }}
                              inline-flex items-center text-white hover:bg-green-700/40 px-2 py-1 rounded-md transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                        </svg>
                        POS
                    </a>
                    <a href="{{ url('/order-history') }}"
                       class="{{ request()->is('order-history') ? 'bg-green-700/40' : '' }}
                              inline-flex items-center text-white hover:bg-green-700/40 px-2 py-1 rounded-md transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z" clip-rule="evenodd" />
                        </svg>
                        Order History
                    </a>
                </div>
            </div>
            <div class="hidden sm:flex sm:items-center">
                <div class="relative flex items-center">
                    <a href="{{ url('/admin') }}" class="mr-4 inline-flex items-center text-white hover:bg-green-700/40 px-2 py-1 rounded-md transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <!-- User name -->
                    <span class="hidden lg:flex mr-2 truncate max-w-[120px] bg-green-700/40 py-1 px-2 rounded-md items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ Auth::user()->name ?? 'User' }}
                    </span>

                    <!-- Sign out button -->
                    <button
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="text-sm text-white bg-green-700/40 hover:bg-green-700/70 py-1 px-3 rounded-md transition-colors"
                    >
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span class="hidden md:inline">Logout</span>
                        </span>
                    </button>
                    <form id="logout-form" action="{{ url('admin/logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
