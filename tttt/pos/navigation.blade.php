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
                    <a href="{{ url('/pos/orders') }}"
                       class="{{ request()->is('pos/orders') ? 'bg-green-700/40' : '' }}
                              inline-flex items-center text-white hover:bg-green-700/40 px-2 py-1 rounded-md transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                        </svg>
                        Orders
                    </a>
                </div>
            </div>
            <div class="hidden sm:flex sm:items-center">
                <div class="relative flex items-center">
                    <a href="{{ url('/admin') }}" class="mr-4 inline-flex items-center text-white hover:bg-green-700/40 px-2 py-1 rounded-md transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
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
                            <span class="hidden md:inline">Sign out</span>
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
