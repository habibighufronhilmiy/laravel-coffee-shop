<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Tens Coffee') }} - Kopi Terbaik untuk Harimu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Playfair Display', 'serif'],
                        sans: ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'fade-up': 'fadeUp 0.6s ease-out',
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slow-spin': 'slowSpin 8s linear infinite',
                    },
                    keyframes: {
                        fadeUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slowSpin: {
                            '0%': { transform: 'rotate(0deg)' },
                            '100%': { transform: 'rotate(360deg)' },
                        },
                    },
                },
            },
        }
    </script>
    <style>
        html { scroll-behavior: smooth; }
        .text-shadow { text-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
        .hero-clip { clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%); }
    </style>
</head>
<body class="font-sans bg-blue-50 text-gray-900 antialiased">

    {{-- Navbar --}}
    <nav x-data="{ mobileOpen: false, scrolled: false }"
         @scroll.window="scrolled = window.scrollY > 40"
         class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
         :class="scrolled ? 'bg-white/95 backdrop-blur-lg shadow-lg' : 'bg-transparent'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-18 items-center">
                <a href="#" class="flex items-center gap-2.5 group">
                    <img src="/img/logo_tens2.jpg" alt="Tens Coffee" class="h-10 w-auto transition-transform duration-300 group-hover:rotate-12">
                    <span class="text-2xl font-bold"
                          :class="scrolled ? 'bg-gradient-to-r from-blue-700 to-indigo-600 bg-clip-text text-transparent' : 'text-white text-shadow'">
                        Tens Coffee
                    </span>
                </a>

                <div class="hidden md:flex items-center gap-1">
                    <a href="#tentang" class="px-4 py-2 rounded-xl text-sm font-medium transition"
                       :class="scrolled ? 'text-blue-700 hover:bg-blue-50' : 'text-white/90 hover:text-white hover:bg-white/10'">
                        Tentang
                    </a>
                    <a href="#menu" class="px-4 py-2 rounded-xl text-sm font-medium transition"
                       :class="scrolled ? 'text-blue-700 hover:bg-blue-50' : 'text-white/90 hover:text-white hover:bg-white/10'">
                        Menu
                    </a>
                    <a href="#keunggulan" class="px-4 py-2 rounded-xl text-sm font-medium transition"
                       :class="scrolled ? 'text-blue-700 hover:bg-blue-50' : 'text-white/90 hover:text-white hover:bg-white/10'">
                        Keunggulan
                    </a>
                    <a href="#testimoni" class="px-4 py-2 rounded-xl text-sm font-medium transition"
                       :class="scrolled ? 'text-blue-700 hover:bg-blue-50' : 'text-white/90 hover:text-white hover:bg-white/10'">
                        Testimoni
                    </a>
                </div>

                <div class="hidden md:flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="px-5 py-2 rounded-xl text-sm font-medium transition"
                           :class="scrolled ? 'text-blue-700 hover:bg-blue-50' : 'text-white/90 hover:text-white hover:bg-white/10'">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                           class="px-6 py-2.5 rounded-xl text-sm font-bold text-white transition shadow-lg hover:shadow-xl active:scale-95"
                           :class="scrolled ? 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700' : 'bg-white/20 backdrop-blur-sm hover:bg-white/30 border border-white/30'">
                            Daftar Sekarang
                        </a>
                    @else
                        <a href="{{ route('menu') }}" class="px-5 py-2 rounded-xl text-sm font-medium transition"
                           :class="scrolled ? 'text-blue-700 hover:bg-blue-50' : 'text-white/90 hover:text-white hover:bg-white/10'">
                            Menu
                        </a>
                        <a href="{{ route('orders') }}" class="px-5 py-2 rounded-xl text-sm font-medium transition"
                           :class="scrolled ? 'text-blue-700 hover:bg-blue-50' : 'text-white/90 hover:text-white hover:bg-white/10'">
                            Pesanan
                        </a>
                        <a href="{{ route('tracking') }}" class="px-5 py-2 rounded-xl text-sm font-medium transition"
                           :class="scrolled ? 'text-blue-700 hover:bg-blue-50' : 'text-white/90 hover:text-white hover:bg-white/10'">
                            Lacak
                        </a>
                        <a href="{{ route('profile') }}" class="px-5 py-2 rounded-xl text-sm font-medium transition"
                           :class="scrolled ? 'text-blue-700 hover:bg-blue-50' : 'text-white/90 hover:text-white hover:bg-white/10'">
                            Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                               class="px-6 py-2.5 rounded-xl text-sm font-bold text-white transition shadow-lg hover:shadow-xl active:scale-95"
                               :class="scrolled ? 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700' : 'bg-white/20 backdrop-blur-sm hover:bg-white/30 border border-white/30'">
                                Logout
                            </button>
                        </form>
                    @endauth
                </div>

                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-xl transition"
                        :class="scrolled ? 'text-blue-700 hover:bg-blue-50' : 'text-white hover:bg-white/10'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div x-show="mobileOpen" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="md:hidden pb-5 space-y-1">
                <a href="#tentang" @click="mobileOpen = false" class="block px-4 py-3 rounded-xl text-blue-700 hover:bg-blue-50 font-medium">Tentang</a>
                <a href="#menu" @click="mobileOpen = false" class="block px-4 py-3 rounded-xl text-blue-700 hover:bg-blue-50 font-medium">Menu</a>
                <a href="#keunggulan" @click="mobileOpen = false" class="block px-4 py-3 rounded-xl text-blue-700 hover:bg-blue-50 font-medium">Keunggulan</a>
                <a href="#testimoni" @click="mobileOpen = false" class="block px-4 py-3 rounded-xl text-blue-700 hover:bg-blue-50 font-medium">Testimoni</a>
                <hr class="my-3 border-blue-200">
                @guest
                    <a href="{{ route('login') }}" class="block px-4 py-3 rounded-xl text-blue-700 hover:bg-blue-50 font-medium">Login</a>
                    <a href="{{ route('register') }}" class="block px-4 py-3 rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 font-bold text-center">Daftar Sekarang</a>
                @else
                    <a href="{{ route('menu') }}" class="block px-4 py-3 rounded-xl text-blue-700 hover:bg-blue-50 font-medium">Menu</a>
                    <a href="{{ route('orders') }}" class="block px-4 py-3 rounded-xl text-blue-700 hover:bg-blue-50 font-medium">Pesanan</a>
                    <a href="{{ route('tracking') }}" class="block px-4 py-3 rounded-xl text-blue-700 hover:bg-blue-50 font-medium">Lacak</a>
                    <a href="{{ route('profile') }}" class="block px-4 py-3 rounded-xl text-blue-700 hover:bg-blue-50 font-medium">Profil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-3 rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 font-bold text-center">Logout</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="relative min-h-screen flex items-center overflow-hidden hero-clip">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900"></div>
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-20 left-10 w-72 h-72 bg-blue-400 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-indigo-400 rounded-full blur-[120px]"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-blue-300 rounded-full blur-[150px]"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 lg:py-40">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20">
                        <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                        <span class="text-white/90 text-sm font-medium">Now Open at Multiple Locations</span>
                    </div>

                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-white leading-tight text-shadow">
                        Wake Up & Smell
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-indigo-300 mt-2">
                            The Coffee
                        </span>
                    </h1>

                    <p class="text-lg sm:text-xl text-white/80 max-w-lg leading-relaxed">
                        Nikmati secangkir kopi pilihan dari biji kopi terbaik Nusantara. Temani setiap momen berhargamu dengan cita rasa yang autentik.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        @guest
                            <a href="{{ route('register') }}"
                               class="group px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-2xl shadow-2xl hover:shadow-blue-500/30 hover:from-blue-500 hover:to-indigo-500 transition-all duration-300 active:scale-95">
                                Pesan Sekarang
                                <span class="inline-block transition-transform duration-300 group-hover:translate-x-1 ml-2">→</span>
                            </a>
                        @else
                            <a href="{{ route('menu') }}"
                               class="group px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-2xl shadow-2xl hover:shadow-blue-500/30 hover:from-blue-500 hover:to-indigo-500 transition-all duration-300 active:scale-95">
                                Pesan Sekarang
                                <span class="inline-block transition-transform duration-300 group-hover:translate-x-1 ml-2">→</span>
                            </a>
                        @endauth
                        <a href="#tentang"
                           class="px-8 py-4 bg-white/10 backdrop-blur-sm border border-white/25 text-white font-medium rounded-2xl hover:bg-white/20 transition-all duration-300">
                            Pelajari Lebih Lanjut
                        </a>
                    </div>

                    <div class="flex items-center gap-8 pt-4">
                        <div class="text-center">
                            <span class="block text-3xl font-bold text-white">50+</span>
                            <span class="text-white/60 text-sm">Menu Variasi</span>
                        </div>
                        <div class="w-px h-12 bg-white/20"></div>
                        <div class="text-center">
                            <span class="block text-3xl font-bold text-white">5</span>
                            <span class="text-white/60 text-sm">Outlet Strategis</span>
                        </div>
                        <div class="w-px h-12 bg-white/20"></div>
                        <div class="text-center">
                            <span class="block text-3xl font-bold text-white">1000+</span>
                            <span class="text-white/60 text-sm">Pelanggan Puas</span>
                        </div>
                    </div>
                </div>

                <div class="relative hidden lg:flex items-center justify-center animate-fade-in">
                    <div class="absolute w-96 h-96 rounded-full bg-gradient-to-br from-blue-400/30 to-indigo-600/30 blur-3xl"></div>
                    <div class="relative w-80 h-80 rounded-full bg-gradient-to-br from-blue-500 to-indigo-700 p-1 animate-slow-spin">
                        <div class="w-full h-full rounded-full bg-blue-900 flex items-center justify-center">
                            <img src="/img/logo_tens2.jpg" alt="Tens Coffee" class="w-full h-full object-contain p-8">
                        </div>
                    </div>
                    <div class="absolute -bottom-4 -right-4 bg-white rounded-2xl shadow-2xl p-5 animate-fade-up">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">⭐</span>
                            <div>
                                <p class="font-bold text-gray-800">4.9 Rating</p>
                                <p class="text-sm text-gray-500">Dari 1.200+ review</p>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -top-4 -left-4 bg-white rounded-2xl shadow-2xl p-4 animate-fade-up" style="animation-delay: 0.3s;">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">🏆</span>
                            <span class="font-semibold text-gray-800">Kopi Terbaik 2024</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute bottom-0 inset-x-0 h-32 bg-gradient-to-t from-blue-50 to-transparent"></div>
    </section>

    {{-- Tentang Section --}}
    <section id="tentang" class="py-24 lg:py-32 bg-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="relative">
                    <div class="relative z-10 rounded-3xl overflow-hidden shadow-2xl">
                        <div class="aspect-[4/3] bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                            <img src="/img/logo_tens2.jpg" alt="Tens Coffee" class="w-full h-full object-contain p-12 opacity-60">
                        </div>
                    </div>
                    <div class="absolute -bottom-6 -right-6 w-48 h-48 bg-gradient-to-br from-blue-300 to-indigo-500 rounded-3xl -z-10"></div>
                    <div class="absolute -top-6 -left-6 w-32 h-32 bg-blue-100 rounded-3xl -z-10"></div>
                </div>

                <div class="space-y-6">
                    <span class="inline-block px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">Tentang Kami</span>
                    <h2 class="text-4xl sm:text-5xl font-display font-bold text-gray-900 leading-tight">
                        Lebih dari Sekadar<br>
                        <span class="text-blue-600">Secangkir Kopi</span>
                    </h2>
                    <p class="text-gray-600 leading-relaxed text-lg">
                        Tens Coffee hadir untuk memberikan pengalaman ngopi terbaik dengan biji kopi pilihan dari berbagai daerah di Indonesia. Kami percaya bahwa setiap cangkir kopi memiliki cerita dan mampu menghadirkan kebahagiaan tersendiri.
                    </p>
                    <p class="text-gray-500 leading-relaxed">
                        Dari proses pemilihan biji kopi hingga penyajian, kami menjaga kualitas agar setiap tegukan memberikan kenikmatan yang maksimal. Kami juga berkomitmen untuk mendukung petani kopi lokal dengan sistem perdagangan yang adil.
                    </p>
                    <div class="flex flex-wrap gap-6 pt-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-xl">🌱</div>
                            <div>
                                <p class="font-semibold text-gray-800">100% Biji Kopi Pilihan</p>
                                <p class="text-sm text-gray-500">Dari petani lokal terbaik</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-xl">🔥</div>
                            <div>
                                <p class="font-semibold text-gray-800">Roasting Artisan</p>
                                <p class="text-sm text-gray-500">Teknik sangrai sempurna</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Menu Section --}}
    <section id="menu" x-data="menuApp()" class="py-24 lg:py-32 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16 space-y-4">
                <span class="inline-block px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">Menu Kami</span>
                <h2 class="text-4xl sm:text-5xl font-display font-bold text-gray-900">
                    Jelajahi <span class="text-blue-600">Menu Kami</span>
                </h2>
                <p class="text-gray-500 text-lg">
                    Berbagai pilihan menu yang siap memanjakan lidahmu, dari kopi klasik hingga kreasi modern.
                </p>
            </div>

            <div x-show="menu.length === 0" class="text-center py-16">
                <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-gray-400">Memuat menu...</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <template x-for="item in menu" :key="item.id">
                    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden card-hover flex flex-col">
                        <div class="h-48 bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center overflow-hidden">
                            <template x-if="item.foto_menu">
                                <img :src="'/storage/' + item.foto_menu" :alt="item.nama_menu" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                            </template>
                            <template x-if="!item.foto_menu">
                                <span class="text-4xl text-gray-300 font-bold" x-text="item.nama_menu?.charAt(0) || '☕'"></span>
                            </template>
                        </div>
                        <div class="p-5 flex flex-col flex-1">
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="text-lg font-semibold text-gray-800 leading-tight" x-text="item.nama_menu"></h3>
                            </div>
                            <p x-show="item.kategori" class="text-xs text-blue-600 font-medium mb-2" x-text="item.kategori?.nama_kategori || ''"></p>
                            <p class="text-gray-500 text-sm mb-1 line-clamp-2" x-text="item.deskripsi || ''"></p>
                            <p class="text-blue-700 font-bold text-xl mt-auto pt-3 border-t border-gray-100" x-text="'Rp' + item.harga.toLocaleString('id-ID')"></p>
                        </div>
                    </div>
                </template>
            </div>

            <div class="text-center mt-12">
                <a href="/menu"
                   class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl active:scale-95">
                    Lihat Semua Menu
                    <span>→</span>
                </a>
            </div>
        </div>
    </section>

    {{-- Keunggulan Section --}}
    <section id="keunggulan" class="py-24 lg:py-32 bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-20 w-64 h-64 bg-blue-400 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-10 right-20 w-80 h-80 bg-indigo-400 rounded-full blur-[120px]"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16 space-y-4">
                <span class="inline-block px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 text-white rounded-full text-sm font-medium">Keunggulan</span>
                <h2 class="text-4xl sm:text-5xl font-display font-bold text-white">
                    Kenapa Memilih <span class="text-blue-300">Tens Coffee</span>?
                </h2>
                <p class="text-white/70 text-lg">
                    Kami berkomitmen memberikan yang terbaik untuk setiap pelanggan.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="group bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-8 hover:bg-white/10 transition-all duration-300">
                    <div class="w-16 h-16 rounded-2xl bg-blue-500/20 flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">🌿</div>
                    <h3 class="text-xl font-bold text-white mb-3">Biji Kopi Premium</h3>
                    <p class="text-white/70 leading-relaxed">Kami hanya menggunakan biji kopi pilihan terbaik dari petani lokal Indonesia yang dirawat dengan penuh dedikasi.</p>
                </div>

                <div class="group bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-8 hover:bg-white/10 transition-all duration-300">
                    <div class="w-16 h-16 rounded-2xl bg-blue-500/20 flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">👨‍🍳</div>
                    <h3 class="text-xl font-bold text-white mb-3">Barista Profesional</h3>
                    <p class="text-white/70 leading-relaxed">Tim barista kami telah terlatih dan berpengalaman dalam menyajikan kopi dengan kualitas terbaik.</p>
                </div>

                <div class="group bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-8 hover:bg-white/10 transition-all duration-300">
                    <div class="w-16 h-16 rounded-2xl bg-blue-500/20 flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">🏪</div>
                    <h3 class="text-xl font-bold text-white mb-3">Outlet Strategis</h3>
                    <p class="text-white/70 leading-relaxed">Lokasi outlet kami mudah dijangkau dengan suasana yang nyaman untuk bekerja atau bersantai.</p>
                </div>

                <div class="group bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-8 hover:bg-white/10 transition-all duration-300">
                    <div class="w-16 h-16 rounded-2xl bg-blue-500/20 flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">📱</div>
                    <h3 class="text-xl font-bold text-white mb-3">Pemesanan Mudah</h3>
                    <p class="text-white/70 leading-relaxed">Pesan melalui aplikasi kami dengan mudah. Bayar online dan nikmati kopi tanpa antri.</p>
                </div>

                <div class="group bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-8 hover:bg-white/10 transition-all duration-300">
                    <div class="w-16 h-16 rounded-2xl bg-blue-500/20 flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">💚</div>
                    <h3 class="text-xl font-bold text-white mb-3">Fair Trade</h3>
                    <p class="text-white/70 leading-relaxed">Kami mendukung kesejahteraan petani kopi dengan sistem perdagangan yang adil dan berkelanjutan.</p>
                </div>

                <div class="group bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-8 hover:bg-white/10 transition-all duration-300">
                    <div class="w-16 h-16 rounded-2xl bg-blue-500/20 flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">🎉</div>
                    <h3 class="text-xl font-bold text-white mb-3">Program Loyalty</h3>
                    <p class="text-white/70 leading-relaxed">Dapatkan poin setiap pembelian dan tukarkan dengan berbagai hadiah dan diskon menarik.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Testimoni Section --}}
    <section id="testimoni" class="py-24 lg:py-32 bg-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16 space-y-4">
                <span class="inline-block px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">Testimoni</span>
                <h2 class="text-4xl sm:text-5xl font-display font-bold text-gray-900">
                    Apa Kata <span class="text-blue-600">Mereka</span>
                </h2>
                <p class="text-gray-500 text-lg">
                    Pengalaman nyata dari para pelanggan setia Tens Coffee.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-100 card-hover">
                    <div class="flex gap-1 mb-4">
                        <span class="text-yellow-400">⭐</span>
                        <span class="text-yellow-400">⭐</span>
                        <span class="text-yellow-400">⭐</span>
                        <span class="text-yellow-400">⭐</span>
                        <span class="text-yellow-400">⭐</span>
                    </div>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        "Kopinya enak banget! Apalagi Caffe Latte-nya, benar-benar creamy dan rich. Tempatnya juga cozy buat WFH."
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center text-xl font-bold text-blue-700">A</div>
                        <div>
                            <p class="font-semibold text-gray-800">Andi Pratama</p>
                            <p class="text-sm text-gray-500">Mahasiswa</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-100 card-hover">
                    <div class="flex gap-1 mb-4">
                        <span class="text-yellow-400">⭐</span>
                        <span class="text-yellow-400">⭐</span>
                        <span class="text-yellow-400">⭐</span>
                        <span class="text-yellow-400">⭐</span>
                        <span class="text-yellow-400">⭐</span>
                    </div>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        "Pesenannya gampang banget lewat app. Tinggal pesan, bayar, tinggal ambil. Hemat waktu banget!"
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center text-xl font-bold text-blue-700">S</div>
                        <div>
                            <p class="font-semibold text-gray-800">Siti Rahmawati</p>
                            <p class="text-sm text-gray-500">Digital Marketer</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-100 card-hover">
                    <div class="flex gap-1 mb-4">
                        <span class="text-yellow-400">⭐</span>
                        <span class="text-yellow-400">⭐</span>
                        <span class="text-yellow-400">⭐</span>
                        <span class="text-yellow-400">⭐</span>
                        <span class="text-yellow-400">⭐</span>
                    </div>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        "Cold Brew-nya juara! Minuman favoritku kalau lagi pengen something fresh dan tidak terlalu pahit."
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center text-xl font-bold text-blue-700">R</div>
                        <div>
                            <p class="font-semibold text-gray-800">Rizky Hakim</p>
                            <p class="text-sm text-gray-500">Software Engineer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-800 to-indigo-800"></div>
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 right-0 w-96 h-96 bg-blue-300 rounded-full blur-[150px]"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-400 rounded-full blur-[100px]"></div>
        </div>
        <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8">
            <h2 class="text-4xl sm:text-5xl font-display font-bold text-white leading-tight">
                Siap Menikmati<br>
                <span class="text-blue-300">Kopi Terbaik</span> Hari Ini?
            </h2>
            <p class="text-white/80 text-lg max-w-2xl mx-auto">
                Daftar sekarang dan dapatkan voucher minuman gratis untuk pesanan pertamamu!
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                @guest
                    <a href="{{ route('register') }}"
                       class="px-10 py-4 bg-white text-blue-800 font-bold rounded-2xl hover:bg-blue-50 transition-all shadow-2xl hover:shadow-white/20 active:scale-95 text-lg">
                        Daftar Gratis
                    </a>
                @else
                    <a href="{{ route('menu') }}"
                       class="px-10 py-4 bg-white text-blue-800 font-bold rounded-2xl hover:bg-blue-50 transition-all shadow-2xl hover:shadow-white/20 active:scale-95 text-lg">
                        Pesan Sekarang
                    </a>
                @endauth
                <a href="/menu"
                   class="px-10 py-4 bg-transparent border-2 border-white/40 text-white font-medium rounded-2xl hover:bg-white/10 transition-all text-lg">
                    Lihat Menu
                </a>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-white/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10">
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <img src="/img/logo_tens2.jpg" alt="Tens Coffee" class="h-8 w-auto">
                        <span class="text-xl font-bold text-white">Tens Coffee</span>
                    </div>
                    <p class="leading-relaxed text-sm">
                        Kopi terbaik Nusantara untuk setiap momen berhargamu. #TensTheMoment
                    </p>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 rounded-xl bg-white/5 hover:bg-white/10 flex items-center justify-center transition text-lg">📷</a>
                        <a href="#" class="w-10 h-10 rounded-xl bg-white/5 hover:bg-white/10 flex items-center justify-center transition text-lg">💬</a>
                        <a href="#" class="w-10 h-10 rounded-xl bg-white/5 hover:bg-white/10 flex items-center justify-center transition text-lg">🐦</a>
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="font-semibold text-white">Menu</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#tentang" class="hover:text-white transition">Tentang</a></li>
                        <li><a href="#menu" class="hover:text-white transition">Menu</a></li>
                        <li><a href="#keunggulan" class="hover:text-white transition">Keunggulan</a></li>
                        <li><a href="#testimoni" class="hover:text-white transition">Testimoni</a></li>
                    </ul>
                </div>

                <div class="space-y-4">
                    <h4 class="font-semibold text-white">Akun</h4>
                    <ul class="space-y-2 text-sm">
                        @guest
                            <li><a href="{{ route('login') }}" class="hover:text-white transition">Login</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-white transition">Daftar</a></li>
                        @else
                            <li><a href="{{ route('menu') }}" class="hover:text-white transition">Menu</a></li>
                            <li><a href="{{ route('orders') }}" class="hover:text-white transition">Pesanan Saya</a></li>
                        @endauth
                        <li><a href="/menu" class="hover:text-white transition">Pesan Sekarang</a></li>
                    </ul>
                </div>

                <div class="space-y-4">
                    <h4 class="font-semibold text-white">Kontak</h4>
                    <ul class="space-y-2 text-sm text-white/70">
                        <li class="flex items-center gap-2">📍 Jakarta, Indonesia</li>
                        <li class="flex items-center gap-2">📧 hello@tenscoffee.id</li>
                        <li class="flex items-center gap-2">📞 (021) 1234-5678</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/10 mt-12 pt-8 text-center text-sm text-white/60">
                <p>&copy; {{ date('Y') }} Tens Coffee. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        function menuApp() {
            return {
                menu: [],
                init() {
                    axios.get('/api/menu').then(res => this.menu = res.data);
                }
            }
        }
    </script>
</body>
</html>
