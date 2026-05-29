@extends('layouts.app')

@section('title', 'Menu')

@section('content')
<div x-data="menuApp()">
    {{-- Outlet Picker --}}
    @auth
    <div x-data="outletPickerApp()" x-cloak>
        <div x-show="!outletDipilih" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-2xl text-center">
            <p class="text-blue-800 font-medium mb-2">📍 Pilih outlet Tens Coffee terdekat</p>
            <p class="text-blue-600 text-sm mb-3">Pilih outlet untuk melihat menu dan melakukan pemesanan</p>
            <button @click="bukaModal()" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl hover:bg-blue-700 transition font-medium shadow-sm">
                Pilih Outlet
            </button>
        </div>

        <div x-show="outletDipilih" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-2xl flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-2xl">📍</span>
                <div>
                    <p class="font-medium text-blue-800" x-text="outletDipilih.nama"></p>
                    <p class="text-xs text-blue-600" x-text="outletDipilih.alamat"></p>
                    <p x-show="outletDipilih.jarak_km" class="text-xs text-blue-500" x-text="'~' + outletDipilih.jarak_km + ' km'"></p>
                </div>
            </div>
            <button @click="bukaModal()" class="text-blue-600 hover:text-blue-800 text-sm font-medium underline">Ganti</button>
        </div>

        {{-- Modal Pilih Outlet --}}
        <div x-show="modalTerbuka" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4"
            @click.self="modalTerbuka = false">
            <div class="absolute inset-0 bg-black/50"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
                <div class="p-5 border-b flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">Pilih Outlet Tens Coffee</h3>
                    <button @click="modalTerbuka = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                </div>
                <div class="p-5 overflow-y-auto flex-1">
                    <div x-show="deteksiLokasiLoading" class="text-center py-8">
                        <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                        <p class="text-gray-500 text-sm">Mendeteksi lokasi Anda...</p>
                    </div>

                        <div x-show="!deteksiLokasiLoading" class="space-y-3">
                        <template x-if="outlets.length === 0">
                            <div class="text-center py-8 text-gray-400">
                                <span class="text-5xl block mb-3">📍</span>
                                <p>Tidak ada outlet tersedia</p>
                            </div>
                        </template>
                        <template x-for="outlet in outlets" :key="outlet.id">
                            <div @click="pilihOutlet(outlet)"
                                class="flex items-start gap-4 p-4 rounded-xl border cursor-pointer transition hover:border-blue-400 hover:bg-blue-50"
                                :class="outletDipilih?.id === outlet.id ? 'border-blue-600 bg-blue-50 ring-2 ring-blue-200' : ''">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    <img src="/img/logo_tens2.jpg" alt="Tens Coffee" class="w-full h-full object-contain p-1.5">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-800" x-text="outlet.nama"></p>
                                    <p class="text-sm text-gray-500 truncate" x-text="outlet.alamat"></p>
                                    <div class="flex gap-3 mt-1 text-xs text-gray-400">
                                        <span x-show="outlet.jarak_km" x-text="outlet.jarak_km + ' km'"></span>
                                        <span x-show="outlet.jam_buka" x-text="'🕐 ' + outlet.jam_buka + ' - ' + (outlet.jam_tutup || '')"></span>
                                    </div>
                                </div>
                                <div x-show="outletDipilih?.id === outlet.id" class="text-blue-600 flex-shrink-0">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function outletPickerApp() {
        return {
            modalTerbuka: false,
            outletDipilih: null,
            outlets: [],
            deteksiLokasiLoading: false,

            init() {
                this.outletDipilih = getOutlet();
                window.addEventListener('open-outlet-picker', () => this.bukaModal());
            },

            bukaModal() {
                this.modalTerbuka = true;
                if (this.outlets.length === 0) {
                    this.deteksiLokasi();
                }
            },

            deteksiLokasi() {
                this.deteksiLokasiLoading = true;
                if (!navigator.geolocation) {
                    this.muatSemuaOutlet();
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;
                        axios.get('/api/outlets/nearby?lat=' + lat + '&lng=' + lng)
                            .then(res => { this.outlets = res.data; })
                            .catch(() => { this.muatSemuaOutlet(); })
                            .finally(() => { this.deteksiLokasiLoading = false; });
                    },
                    () => {
                        this.muatSemuaOutlet();
                        this.deteksiLokasiLoading = false;
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            },

            muatSemuaOutlet() {
                axios.get('/api/outlets')
                    .then(res => { this.outlets = res.data; })
                    .catch(() => {});
            },

            pilihOutlet(outlet) {
                this.outletDipilih = outlet;
                simpanOutlet(outlet);
                this.modalTerbuka = false;
            }
        }
    }
    </script>
    @endauth

    {{-- Banner Carousel --}}
    <div x-show="banners.length > 0" class="relative mb-8 overflow-hidden rounded-2xl bg-gray-900" x-data="bannerCarousel()">
        <div class="relative h-48 sm:h-56 md:h-64">
            <template x-for="(banner, i) in banners" :key="i">
                <div x-show="current === i"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-x-8"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 -translate-x-8"
                    class="absolute inset-0">
                    <a :href="banner.link || '#'" :target="banner.link ? '_blank' : '_self'">
                        <img :src="'/storage/' + banner.gambar" :alt="banner.judul"
                            class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <h3 class="text-lg sm:text-xl font-bold" x-text="banner.judul"></h3>
                            <p x-show="banner.deskripsi" class="text-sm text-white/80 mt-1" x-text="banner.deskripsi"></p>
                        </div>
                    </a>
                </div>
            </template>
        </div>
        <div x-show="banners.length > 1" class="absolute bottom-2 inset-x-0 flex justify-center gap-1.5 pb-1">
            <template x-for="(banner, i) in banners" :key="i">
                <button @click="current = i"
                    class="w-2 h-2 rounded-full transition-all duration-300"
                    :class="current === i ? 'bg-white w-5' : 'bg-white/50 hover:bg-white/70'"></button>
            </template>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Menu Tens Coffee</h1>
            <p class="text-gray-500 mt-1">Nikmati kopi terbaik pilihan kami</p>
        </div>
        <div class="relative w-full sm:w-72">
            <input type="text" x-model="searchQuery" placeholder="Cari menu..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm">
            <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

    <div class="flex flex-wrap gap-2 mb-8">
        <button @click="selectedKategori = null"
            :class="!selectedKategori ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-600 border hover:bg-gray-50'"
            class="px-5 py-2 rounded-full text-sm font-medium transition shadow-sm">
            Semua
        </button>
        <template x-for="kategori in kategoris" :key="kategori.id">
            <button @click="selectedKategori = selectedKategori === kategori.id ? null : kategori.id"
                :class="selectedKategori === kategori.id ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-600 border hover:bg-gray-50'"
                class="px-5 py-2 rounded-full text-sm font-medium transition shadow-sm">
                <span x-text="kategori.nama_kategori"></span>
            </button>
        </template>
    </div>

    {{-- Paling Sering Dipesan --}}
    <div x-show="mostOrdered.length > 0" class="mb-10">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">🔥 Paling Sering Dipesan</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <template x-for="item in mostOrdered" :key="'fav-' + item.id">
                <div @click="openItemModal(item)"
                    class="bg-white rounded-2xl shadow-sm border overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                    <div class="h-32 bg-gradient-to-br from-orange-50 to-yellow-50 flex items-center justify-center overflow-hidden">
                        <template x-if="item.foto_menu">
                            <img :src="'/storage/' + item.foto_menu" :alt="item.nama_menu" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!item.foto_menu">
                            <span class="text-3xl text-gray-300 font-bold" x-text="item.nama_menu?.charAt(0) || '☕'"></span>
                        </template>
                    </div>
                    <div class="p-3">
                        <h4 class="font-semibold text-gray-800 text-sm leading-tight" x-text="item.nama_menu"></h4>
                        <p class="text-blue-700 font-bold text-sm mt-1" x-text="'Rp ' + item.harga.toLocaleString('id-ID')"></p>
                        <span class="text-xs text-gray-400" x-text="item.order_count + '× dipesan'"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div x-show="filteredMenu.length === 0" class="text-center py-20 text-gray-400">
        <span class="text-7xl block mb-4">🔍</span>
        <p class="text-xl">Menu tidak ditemukan</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <template x-for="item in filteredMenu" :key="item.id">
            <div class="bg-white rounded-2xl shadow-sm border overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col slide-up">
                <div class="h-52 bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center overflow-hidden">
                    <template x-if="item.foto_menu">
                        <img :src="'/storage/' + item.foto_menu" :alt="item.nama_menu" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                    </template>
                    <template x-if="!item.foto_menu">
                        <span class="text-4xl text-gray-300 font-bold" x-text="item.nama_menu?.charAt(0) || '☕'"></span>
                    </template>
                </div>
                <div class="p-5 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-semibold text-gray-800 leading-tight" x-text="item.nama_menu"></h3>
                        <button @click="toggleWishlist(item)" class="text-xl flex-shrink-0 ml-2 transition hover:scale-110"
                            :class="item.wishlisted ? 'text-red-500' : 'text-gray-300 hover:text-red-400'">
                            <span x-text="item.wishlisted ? '❤️' : '🤍'"></span>
                        </button>
                    </div>
                    <button @click="bukaReviewModal(item)" class="flex items-center gap-1 mb-1 hover:opacity-80 transition">
                        <template x-for="i in 5" :key="i">
                            <span class="text-xs" x-text="i <= Math.round(item.average_rating) ? '⭐' : '☆'"></span>
                        </template>
                        <span class="text-xs text-gray-400 ml-1" x-text="'(' + item.ratings_count + ')'"></span>
                    </button>
                    <p class="text-gray-500 text-sm mb-1 line-clamp-2" x-text="item.deskripsi || ''"></p>
                    <p class="text-blue-700 font-bold text-xl mb-1" x-text="'Rp ' + item.harga.toLocaleString('id-ID')"></p>
                    <p class="text-xs text-gray-400 mb-4" x-text="item.kategori?.nama_kategori || ''"></p>
                    <div class="mt-auto flex items-center justify-between pt-3 border-t border-gray-100">
                        <span class="text-sm font-medium" :class="item.stok > 0 ? 'text-green-600' : 'text-red-500'">
                            <span x-text="item.stok > 0 ? 'Stok: ' + item.stok : 'Habis'"></span>
                        </span>
                        <button @click="openItemModal(item)" :disabled="item.stok < 1"
                            class="bg-blue-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-blue-700 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed transition active:scale-95">
                            <span x-text="item.stok > 0 ? '+ Keranjang' : 'Habis'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Modal Pilih Varian & Opsi --}}
    <div x-show="showItemModal" x-cloak class="fixed inset-0 z-[70] flex items-center justify-center p-4"
        @click.self="closeItemModal()">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            {{-- Header --}}
            <div class="p-5 border-b flex items-center justify-between sticky top-0 bg-white z-10">
                <div>
                    <h3 class="text-lg font-bold text-gray-800" x-text="modalItem?.nama_menu"></h3>
                    <p class="text-sm text-gray-500" x-text="'Rp ' + modalItem?.harga.toLocaleString('id-ID')"></p>
                </div>
                <button @click="closeItemModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>

            <div class="p-5 space-y-6">
                {{-- Pilih Varian --}}
                <template x-if="modalItem?.variants?.length > 0">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Varian</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="v in modalItem.variants" :key="v.id">
                                <button @click="pilihVariant(v)"
                                    class="p-3 rounded-xl border text-left transition"
                                    :class="selectedVariant?.id === v.id ? 'border-blue-600 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200 hover:border-blue-300'">
                                    <span class="block text-sm font-medium text-gray-800" x-text="v.nama"></span>
                                    <span x-show="v.harga_tambahan > 0" class="text-xs text-blue-600" x-text="'+Rp ' + v.harga_tambahan.toLocaleString('id-ID')"></span>
                                    <span x-show="v.stok !== null" class="text-xs text-gray-400" x-text="'Stok: ' + v.stok"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Option Groups --}}
                <template x-for="group in modalItem?.option_groups" :key="group.id">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3" x-text="group.nama"></h4>
                        <template x-if="group.tipe === 'single'">
                            <div class="flex flex-wrap gap-2">
                                <template x-for="opt in group.items" :key="opt.id">
                                    <button @click="pilihOption(group, opt)"
                                        class="px-4 py-2.5 rounded-xl border text-sm transition"
                                        :class="isOptionSelected(group.id, opt.id) ? 'border-blue-600 bg-blue-50 ring-2 ring-blue-200 text-blue-700 font-medium' : 'border-gray-200 hover:border-blue-300 text-gray-700'">
                                        <span x-text="opt.nama"></span>
                                        <span x-show="opt.harga_tambahan > 0" class="text-blue-600 ml-1" x-text="'+' + opt.harga_tambahan.toLocaleString('id-ID')"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                        <template x-if="group.tipe === 'multiple'">
                            <div class="flex flex-wrap gap-2">
                                <template x-for="opt in group.items" :key="opt.id">
                                    <button @click="toggleAddon(group, opt)"
                                        class="px-4 py-2.5 rounded-xl border text-sm transition"
                                        :class="isOptionSelected(group.id, opt.id) ? 'border-green-500 bg-green-50 ring-2 ring-green-200 text-green-700 font-medium' : 'border-gray-200 hover:border-green-300 text-gray-700'">
                                        <span x-text="opt.nama"></span>
                                        <span x-show="opt.harga_tambahan > 0" class="text-green-600 ml-1" x-text="'+Rp ' + opt.harga_tambahan.toLocaleString('id-ID')"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Ringkasan Harga --}}
                <div class="bg-gray-50 rounded-xl p-4 space-y-1.5 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Harga dasar</span>
                        <span x-text="'Rp ' + (modalItem?.harga || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <div x-show="selectedVariant?.harga_tambahan > 0" class="flex justify-between text-gray-600">
                        <span>Varian <span x-text="selectedVariant?.nama"></span></span>
                        <span x-text="'+Rp ' + (selectedVariant?.harga_tambahan || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <template x-for="opt in selectedOptions" :key="opt.item_id">
                        <div x-show="opt.harga_tambahan > 0" class="flex justify-between text-gray-600">
                            <span x-text="opt.item_name"></span>
                            <span x-text="'+Rp ' + opt.harga_tambahan.toLocaleString('id-ID')"></span>
                        </div>
                    </template>
                    <div class="flex justify-between font-bold text-gray-800 pt-2 border-t border-gray-200">
                        <span>Total</span>
                        <span class="text-blue-700" x-text="'Rp ' + calculatedPrice.toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </div>

            <div class="p-5 border-t sticky bottom-0 bg-white">
                <button @click="addCurrentToCart()"
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition font-medium shadow-md active:scale-[0.98]">
                    + Tambahkan ke Keranjang
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Review Detail --}}
    <div x-show="showReviewModal" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center p-4"
        @click.self="showReviewModal = false">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col">
            <div class="p-5 border-b flex items-center justify-between sticky top-0 bg-white z-10">
                <div>
                    <h3 class="text-lg font-bold text-gray-800" x-text="reviewItem?.nama_menu"></h3>
                    <p class="text-sm text-gray-500" x-text="'⭐ ' + (reviewItem?.average_rating || 0) + ' dari ' + (reviewItem?.ratings_count || 0) + ' ulasan'"></p>
                </div>
                <button @click="showReviewModal = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <div class="p-5 overflow-y-auto flex-1">
                <div x-show="reviewsLoading" class="text-center py-8">
                    <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                    <p class="text-gray-500 text-sm">Memuat ulasan...</p>
                </div>
                <template x-if="!reviewsLoading && reviews.length === 0">
                    <div class="text-center py-8 text-gray-400">
                        <span class="text-5xl block mb-3">💬</span>
                        <p>Belum ada ulasan</p>
                    </div>
                </template>
                <div x-show="!reviewsLoading" class="space-y-4">
                    <template x-for="r in reviews" :key="r.id">
                        <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    <template x-if="r.user?.avatar">
                                        <img :src="'/storage/' + r.user.avatar" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!r.user?.avatar">
                                        <span class="text-sm font-bold text-blue-600" x-text="(r.user?.name || '?').charAt(0).toUpperCase()"></span>
                                    </template>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800" x-text="r.user?.name || 'Anonymous'"></p>
                                    <div class="flex items-center gap-1">
                                        <template x-for="i in 5" :key="i">
                                            <span class="text-xs" x-text="i <= r.rating ? '⭐' : '☆'"></span>
                                        </template>
                                        <span class="text-xs text-gray-400 ml-1" x-text="formatTanggal(r.created_at)"></span>
                                    </div>
                                </div>
                            </div>
                            <p x-show="r.review" class="text-sm text-gray-600 ml-12" x-text="r.review"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function bannerCarousel() {
    return {
        current: 0,
        interval: null,
        init() {
            const banners = document.querySelector('[x-data="menuApp()"]')?.__x?.$data?.banners || [];
            if (banners.length > 1) {
                this.interval = setInterval(() => {
                    this.current = (this.current + 1) % banners.length;
                }, 5000);
            }
        },
        destroy() {
            if (this.interval) clearInterval(this.interval);
        }
    }
}

function menuApp() {
    return {
        menu: [],
        kategoris: [],
        banners: [],
        selectedKategori: null,
        searchQuery: '',

        showItemModal: false,
        modalItem: null,
        selectedVariant: null,
        selectedOptions: [],

        get filteredMenu() {
            let items = this.menu;
            if (this.selectedKategori) {
                items = items.filter(item => item.kategori_id === this.selectedKategori);
            }
            if (this.searchQuery.trim()) {
                const q = this.searchQuery.toLowerCase();
                items = items.filter(item => item.nama_menu.toLowerCase().includes(q));
            }
            return items;
        },

        get mostOrdered() {
            return this.menu
                .filter(item => item.order_count > 0)
                .sort((a, b) => b.order_count - a.order_count)
                .slice(0, 5);
        },

        get calculatedPrice() {
            const item = this.modalItem;
            if (!item) return 0;
            let total = item.harga;
            if (this.selectedVariant) {
                total += this.selectedVariant.harga_tambahan || 0;
            }
            this.selectedOptions.forEach(opt => {
                total += opt.harga_tambahan || 0;
            });
            return total;
        },

        init() {
            axios.get('/api/menu').then(res => this.menu = res.data);
            axios.get('/api/kategoris').then(res => this.kategoris = res.data);
            axios.get('/api/banners').then(res => this.banners = res.data);
        },

        openItemModal(item) {
            const token = getToken();
            if (!token) {
                window.location.href = '{{ route("login") }}';
                return;
            }
            this.modalItem = item;
            this.selectedVariant = null;
            this.selectedOptions = [];
            if (item.option_groups) {
                item.option_groups.forEach(group => {
                    const def = group.items?.find(o => o.is_default);
                    if (def && group.tipe === 'single') {
                        this.selectedOptions.push({
                            group_id: group.id,
                            group_name: group.nama,
                            item_id: def.id,
                            item_name: def.nama,
                            harga_tambahan: def.harga_tambahan || 0,
                        });
                    }
                });
            }
            this.showItemModal = true;
        },

        closeItemModal() {
            this.showItemModal = false;
            this.modalItem = null;
            this.selectedVariant = null;
            this.selectedOptions = [];
        },

        pilihVariant(v) {
            this.selectedVariant = v;
        },

        pilihOption(group, opt) {
            this.selectedOptions = this.selectedOptions.filter(o => o.group_id !== group.id);
            this.selectedOptions.push({
                group_id: group.id,
                group_name: group.nama,
                item_id: opt.id,
                item_name: opt.nama,
                harga_tambahan: opt.harga_tambahan || 0,
            });
        },

        toggleAddon(group, opt) {
            const idx = this.selectedOptions.findIndex(o => o.item_id === opt.id);
            if (idx > -1) {
                this.selectedOptions.splice(idx, 1);
            } else {
                this.selectedOptions.push({
                    group_id: group.id,
                    group_name: group.nama,
                    item_id: opt.id,
                    item_name: opt.nama,
                    harga_tambahan: opt.harga_tambahan || 0,
                });
            }
        },

        isOptionSelected(groupId, itemId) {
            return this.selectedOptions.some(o => o.group_id === groupId && o.item_id === itemId);
        },

        addCurrentToCart() {
            const token = getToken();
            if (!token) {
                window.location.href = '{{ route("login") }}';
                return;
            }
            const payload = {
                menu_id: this.modalItem.id,
                jumlah: 1,
            };
            if (this.selectedVariant) {
                payload.menu_variant_id = this.selectedVariant.id;
            }
            if (this.selectedOptions.length > 0) {
                payload.selected_options = this.selectedOptions.map(o => ({
                    group_id: o.group_id,
                    item_id: o.item_id,
                }));
            }
            axios.post('/api/cart/add', payload, {
                headers: { Authorization: 'Bearer ' + token }
            }).then(res => {
                this.closeItemModal();
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: res.data?.message || this.modalItem.nama_menu + ' ditambahkan ke keranjang!', type: 'success' } }));
                window.dispatchEvent(new CustomEvent('cart-updated'));
            }).catch(err => {
                const msg = err.response?.data?.message;
                const status = err.response?.status;
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: msg || 'Gagal menambahkan (error ' + (status || 'network') + ')', type: 'error' } }));
            });
        },

        toggleWishlist(item) {
            const token = getToken();
            if (!token) {
                window.location.href = '{{ route("login") }}';
                return;
            }
            axios.post('/api/wishlists/toggle', { menu_id: item.id }, {
                headers: { Authorization: 'Bearer ' + token }
            }).then(res => {
                item.wishlisted = res.data.wishlisted;
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: res.data.message, type: 'success' } }));
            }).catch(err => {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Gagal', type: 'error' } }));
            });
        },

        showReviewModal: false,
        reviewItem: null,
        reviews: [],
        reviewsLoading: false,

        bukaReviewModal(item) {
            this.reviewItem = item;
            this.showReviewModal = true;
            this.reviews = [];
            this.reviewsLoading = true;
            axios.get('/api/menu/' + item.id + '/ratings')
                .then(res => this.reviews = res.data)
                .catch(() => {})
                .finally(() => { this.reviewsLoading = false; });
        },

        formatTanggal(date) {
            return new Date(date).toLocaleDateString('id-ID', {
                year: 'numeric', month: 'short', day: 'numeric'
            });
        }
    }
}
</script>
@endpush
