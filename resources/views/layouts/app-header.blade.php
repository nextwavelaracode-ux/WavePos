<header class="antialiased sticky top-0 z-[997] bg-white/90 dark:bg-neutral-900/90 backdrop-blur-md border-b border-neutral-200 dark:border-neutral-800">
  <nav class="px-4 lg:px-6 py-2.5">
      <div class="flex flex-wrap justify-between items-center">
          <div class="flex justify-start items-center">
              <button @click="$store.sidebar.toggleExpanded(); $store.sidebar.setMobileOpen($store.sidebar.isExpanded)" aria-expanded="true" aria-controls="sidebar" class="hidden p-2 mr-3 text-neutral-600 rounded cursor-pointer lg:inline hover:text-neutral-900 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:text-white dark:hover:bg-neutral-800 transition-colors">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12"> <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h14M1 6h14M1 11h7"/> </svg>
              </button>
              <button @click="$store.sidebar.setMobileOpen(true)" aria-expanded="true" aria-controls="sidebar" class="p-2 mr-2 text-neutral-600 rounded-lg cursor-pointer lg:hidden hover:text-neutral-900 hover:bg-neutral-100 focus:bg-neutral-100 dark:focus:bg-neutral-800 focus:ring-2 focus:ring-neutral-100 dark:focus:ring-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-white transition-colors">
                <svg class="w-[18px] h-[18px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/></svg>
                <span class="sr-only">Toggle sidebar</span>
              </button>
              <a href="/" class="flex mr-4">
                <img src="/images/logo/wavepos-icon.svg" class="mr-3 h-8" alt="WavePOS Logo" />
                <span class="self-center hidden sm:block text-2xl font-semibold whitespace-nowrap dark:text-white text-brand-600">WavePOS</span>
              </a>
              <form action="#" method="GET" class="hidden lg:block lg:pl-2">
                <label for="topbar-search" class="sr-only">Search</label>
                <div class="relative mt-1 lg:w-96">
                  <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                      <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20"> <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/> </svg>
                  </div>
                  <input type="text" name="search" id="topbar-search" class="bg-neutral-100 border border-transparent text-neutral-700 sm:text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full pl-9 p-2.5 dark:bg-neutral-800 dark:border-transparent dark:placeholder-neutral-500 dark:text-neutral-200 dark:focus:ring-brand-500 dark:focus:border-brand-500 transition-colors" placeholder="Buscar en el sistema...">
                </div>
              </form>
            </div>
          <div class="flex items-center gap-1 lg:order-2">
              @can('pos.ver')
              <a href="{{ url('/caja/pos') }}" class="hidden sm:inline-flex items-center justify-center text-white bg-brand-600 hover:bg-brand-700 focus:ring-4 focus:ring-brand-300 font-medium rounded-lg text-xs px-3 py-1.5 mr-2 dark:bg-brand-500 dark:hover:bg-brand-600 focus:outline-none dark:focus:ring-brand-800 transition-colors">
                  <svg aria-hidden="true" class="mr-1 -ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 2L3 6V20C3 21.1 3.9 22 5 22H19C20.1 22 21 21.1 21 20V6L18 2H6ZM16 16H13V19H11V16H8L12 12L16 16ZM15.55 8C15.06 9.19 13.9 10 12.55 10C11.2 10 10.04 9.19 9.55 8H3V6L6 2H18L21 6V8H15.55Z"></path></svg> 
                  Punto de Venta
              </a>
              @endcan

              <button id="toggleSidebarMobileSearch" type="button" class="p-2 text-neutral-500 rounded-lg lg:hidden hover:text-neutral-900 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-white transition-colors">
                  <span class="sr-only">Search</span>
                  <!-- Search icon -->
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
              </button>

              {{-- Dark mode toggle --}}
              <button
                  @click="$store.theme.toggle()"
                  class="flex items-center justify-center p-2 rounded-lg
                        text-neutral-500 dark:text-neutral-400
                        hover:bg-neutral-100 dark:hover:bg-neutral-800
                        hover:text-neutral-700 dark:hover:text-white
                        transition-colors"
                  title="Toggle theme"
              >
                  {{-- Sun --}}
                  <svg x-show="$store.theme.theme === 'dark'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                  </svg>
                  {{-- Moon --}}
                  <svg x-show="$store.theme.theme !== 'dark'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                  </svg>
              </button>

              <!-- Notifications -->
              <x-header.notification-dropdown />

              <!-- User logic -->
              <x-header.user-dropdown />
          </div>
      </div>
  </nav>
</header>
