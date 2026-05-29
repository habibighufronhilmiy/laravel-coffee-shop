<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tens Coffee') - Tens Coffee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('head')
    <style>
        [x-cloak] { display: none !important; }
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .slide-up { animation: slideUp 0.3s ease-out; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .toast { animation: toastIn 0.4s ease-out, toastOut 0.4s 2.6s ease-in forwards; }
        @keyframes toastIn { from { opacity: 0; transform: translateX(100%); } to { opacity: 1; transform: translateX(0); } }
        @keyframes toastOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(100%); } }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    @auth
        <script>
            @if(session('api_token'))
                localStorage.setItem('token', '{{ session('api_token') }}');
            @endif
            localStorage.setItem('user_name', '{{ Auth::user()->name }}');
            localStorage.setItem('user_email', '{{ Auth::user()->email }}');
        </script>
    @endauth
    @guest
        <script>
            localStorage.removeItem('token');
        </script>
    @endguest

    <nav x-data="navApp()" class="bg-white/80 backdrop-blur-md shadow-sm border-b sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('menu') }}" class="flex items-center gap-2">
                    <img src="/img/logo_tens2.jpg" alt="Tens Coffee" class="h-8 w-auto">
                    <span class="text-xl font-bold bg-gradient-to-r from-blue-700 to-indigo-600 bg-clip-text text-transparent">Tens Coffee</span>
                </a>

                @auth
                <button @click="bukaPemilihOutlet()"
                    class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition"
                    :class="outletDipilih ? 'bg-blue-50 text-blue-700 hover:bg-blue-100' : 'bg-blue-50 text-blue-700 hover:bg-blue-100'">
                    <span x-text="outletDipilih ? '📍' : '⚠️'"></span>
                    <span x-text="outletDipilih ? outletDipilih.nama : 'Pilih Outlet'"></span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                @endauth

                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('menu') }}"
                       class="px-4 py-2 rounded-lg text-gray-600 hover:text-blue-700 hover:bg-blue-50 transition {{ request()->routeIs('menu') ? 'text-blue-700 bg-blue-50 font-medium' : '' }}">
                        Menu
                    </a>

                    @auth
                        <a href="{{ route('cart') }}"
                           class="relative px-4 py-2 rounded-lg text-gray-600 hover:text-blue-700 hover:bg-blue-50 transition {{ request()->routeIs('cart') ? 'text-blue-700 bg-blue-50 font-medium' : '' }}">
                            Keranjang
                            <span x-show="cartCount > 0"
                                  class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold"
                                  x-text="cartCount"></span>
                        </a>

                        <a href="{{ route('orders') }}"
                           class="px-4 py-2 rounded-lg text-gray-600 hover:text-blue-700 hover:bg-blue-50 transition {{ request()->routeIs('orders') ? 'text-blue-700 bg-blue-50 font-medium' : '' }}">
                            Pesanan
                        </a>

                        <a href="{{ route('wishlist') }}"
                           class="px-4 py-2 rounded-lg text-gray-600 hover:text-blue-700 hover:bg-blue-50 transition {{ request()->routeIs('wishlist') ? 'text-blue-700 bg-blue-50 font-medium' : '' }}">
                            Wishlist
                        </a>

                        <a href="{{ route('tracking') }}"
                           class="px-4 py-2 rounded-lg text-gray-600 hover:text-blue-700 hover:bg-blue-50 transition {{ request()->routeIs('tracking') ? 'text-blue-700 bg-blue-50 font-medium' : '' }}">
                            Lacak
                        </a>

                        <a href="{{ route('profile') }}"
                           class="px-4 py-2 rounded-lg text-gray-600 hover:text-blue-700 hover:bg-blue-50 transition {{ request()->routeIs('profile') ? 'text-blue-700 bg-blue-50 font-medium' : '' }}">
                            Profil
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="ml-2">
                            @csrf
                            <button type="submit" onclick="localStorage.removeItem('token');localStorage.removeItem('user_name');localStorage.removeItem('user_email');"
                                class="px-4 py-2 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 transition">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="px-4 py-2 rounded-lg text-gray-600 hover:text-blue-700 hover:bg-blue-50 transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                           class="ml-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-5 py-2 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition font-medium shadow-md">
                            Daftar
                        </a>
                    @endauth
                </div>

                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div x-show="mobileOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="md:hidden pb-4 space-y-1">
                <a href="{{ route('menu') }}" class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('menu') ? 'text-blue-700 bg-blue-50 font-medium' : '' }}">
                    Menu
                </a>
                @auth
                    <button @click="bukaPemilihOutlet()"
                        class="block w-full text-left px-4 py-2 rounded-lg"
                        :class="outletDipilih ? 'text-blue-700 hover:bg-blue-50 font-medium' : 'text-blue-700 hover:bg-blue-50 font-medium'">
                        📍 <span x-text="outletDipilih ? outletDipilih.nama : 'Pilih Outlet'"></span>
                    </button>
                    <a href="{{ route('cart') }}" class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('cart') ? 'text-blue-700 bg-blue-50 font-medium' : '' }}">
                        Keranjang <span x-show="cartCount > 0" class="text-red-500 font-bold" x-text="'(' + cartCount + ')'"></span>
                    </a>
                    <a href="{{ route('orders') }}" class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('orders') ? 'text-blue-700 bg-blue-50 font-medium' : '' }}">
                        Pesanan
                    </a>
                    <a href="{{ route('wishlist') }}" class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('wishlist') ? 'text-blue-700 bg-blue-50 font-medium' : '' }}">
                        ❤️ Wishlist
                    </a>
                    <a href="{{ route('tracking') }}" class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('tracking') ? 'text-blue-700 bg-blue-50 font-medium' : '' }}">
                        Lacak
                    </a>
                    <a href="{{ route('profile') }}" class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-blue-50 hover:text-blue-700 {{ request()->routeIs('profile') ? 'text-blue-700 bg-blue-50 font-medium' : '' }}">
                        Profil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" onclick="localStorage.removeItem('token');localStorage.removeItem('user_name');localStorage.removeItem('user_email');"
                            class="block w-full text-left px-4 py-2 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-blue-50 hover:text-blue-700">Login</a>
                    <a href="{{ route('register') }}" class="block px-4 py-2 rounded-lg text-blue-600 font-medium hover:bg-blue-50">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    <div x-data="toastApp()" x-cloak class="fixed top-20 right-4 z-[100] space-y-2"
         @toast.window="show($event.detail.message, $event.detail.type)">
        <template x-for="(toast, index) in toasts" :key="index">
            <div class="toast px-5 py-3 rounded-lg shadow-lg text-white text-sm font-medium flex items-center gap-2"
                 :class="toast.type === 'success' ? 'bg-green-600' : toast.type === 'error' ? 'bg-red-600' : 'bg-blue-600'">
                <span x-text="toast.type === 'success' ? '✓' : toast.type === 'error' ? '✕' : 'ℹ'"></span>
                <span x-text="toast.message"></span>
            </div>
        </template>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 fade-in">
        @yield('content')
    </main>

    <script>
        function navApp() {
            return {
                mobileOpen: false,
                cartCount: 0,
                outletDipilih: null,
                init() {
                    this.refreshCart();
                    this.loadOutlet();
                    window.addEventListener('cart-updated', () => this.refreshCart());
                    window.addEventListener('outlet-changed', (e) => {
                        this.outletDipilih = e.detail;
                    });
                },
                refreshCart() {
                    const token = getToken();
                    if (token) {
                        axios.get('/api/cart', { headers: { Authorization: 'Bearer ' + token } })
                            .then(res => { this.cartCount = Object.keys(res.data.cart || {}).length; })
                            .catch(() => { this.cartCount = 0; });
                    }
                },
                loadOutlet() {
                    try {
                        const saved = localStorage.getItem('selectedOutlet');
                        if (saved) this.outletDipilih = JSON.parse(saved);
                    } catch(e) {}
                },
                bukaPemilihOutlet() {
                    window.dispatchEvent(new CustomEvent('open-outlet-picker'));
                }
            }
        }

        function toastApp() {
            return {
                toasts: [],
                show(message, type = 'success') {
                    this.toasts.push({ message, type });
                    setTimeout(() => { this.toasts.shift(); }, 3000);
                }
            }
        }

        function getToken() {
            return localStorage.getItem('token') || document.querySelector('meta[name="api-token"]')?.getAttribute('content') || null;
        }

        function getAuthHeaders() {
            const token = getToken();
            return token ? { Authorization: 'Bearer ' + token } : {};
        }

        function simpanOutlet(outlet) {
            localStorage.setItem('selectedOutlet', JSON.stringify(outlet));
            window.dispatchEvent(new CustomEvent('outlet-changed', { detail: outlet }));
        }

        function getOutlet() {
            try {
                const saved = localStorage.getItem('selectedOutlet');
                return saved ? JSON.parse(saved) : null;
            } catch(e) { return null; }
        }

        function showToast(message, type = 'success') {
            console.log('showToast called:', message, type);
            const el = document.querySelector('[x-data="toastApp()"]');
            console.log('toast element found:', el, el?.__x?.$data);
            if (el && el.__x) el.__x.$data.show(message, type);
        }

        window.addEventListener('toast', e => {
            showToast(e.detail.message, e.detail.type);
        });
    </script>
    @stack('scripts')
</body>
</html>