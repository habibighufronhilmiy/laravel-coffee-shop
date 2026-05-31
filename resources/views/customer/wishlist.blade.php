@extends('layouts.app')

@section('title', 'Wishlist Saya')

@section('content')
<div x-data="wishlistApp()" x-cloak>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Wishlist Saya</h1>
        <p class="text-gray-500 mt-1">Menu favorit yang kamu simpan</p>
    </div>

    <template x-if="loading">
        <div class="text-center py-20">
            <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-500">Memuat wishlist...</p>
        </div>
    </template>

    <template x-if="!loading && items.length === 0">
        <div class="text-center py-20 text-gray-400 slide-up">
            <span class="text-8xl block mb-6">🤍</span>
            <p class="text-2xl font-medium text-gray-500 mb-2">Wishlist masih kosong</p>
            <p class="text-gray-400 mb-6">Tambahkan menu favorit kamu dengan menekan tombol hati</p>
            <a href="{{ route('menu') }}"
                class="inline-block bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition font-medium shadow-md">
                Lihat Menu
            </a>
        </div>
    </template>

    <div x-show="!loading && items.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <template x-for="item in items" :key="item.id">
            <div class="bg-white rounded-2xl shadow-sm border overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col slide-up">
                <div class="h-52 bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center overflow-hidden">
                    <template x-if="item.menu?.foto_menu">
                        <img :src="'/storage/' + item.menu.foto_menu" :alt="item.menu.nama_menu" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                    </template>
                    <template x-if="!item.menu?.foto_menu">
                        <span class="text-4xl text-gray-300 font-bold" x-text="item.menu?.nama_menu?.charAt(0) || '☕'"></span>
                    </template>
                </div>
                <div class="p-5 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-semibold text-gray-800 leading-tight" x-text="item.menu?.nama_menu"></h3>
                        <button @click="hapusWishlist(item)" class="text-red-500 text-xl flex-shrink-0 ml-2 transition hover:scale-110">
                            ❤️
                        </button>
                    </div>
                    <div x-show="item.menu?.average_rating" class="flex items-center gap-1 mb-1">
                        <template x-for="i in 5" :key="i">
                            <span class="text-xs" x-text="i <= Math.round(item.menu.average_rating) ? '⭐' : '☆'"></span>
                        </template>
                        <span class="text-xs text-gray-400 ml-1" x-text="'(' + item.menu.ratings_count + ')'"></span>
                    </div>
                    <p class="text-blue-700 font-bold text-xl mb-1" x-text="'Rp' + item.menu?.harga.toLocaleString('id-ID')"></p>
                    <p class="text-xs text-gray-400 mb-4" x-text="item.menu?.kategori?.nama_kategori || ''"></p>
                    <div class="mt-auto flex items-center justify-between pt-3 border-t border-gray-100">
                        <span class="text-sm font-medium" :class="item.menu?.stok > 0 ? 'text-green-600' : 'text-red-500'">
                            <span x-text="item.menu?.stok > 0 ? 'Stok: ' + item.menu.stok : 'Habis'"></span>
                        </span>
                        <a :href="'{{ route('menu') }}'"
                            class="bg-blue-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-blue-700 transition active:scale-95 inline-block">
                            + Keranjang
                        </a>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection

@push('scripts')
<script>
function wishlistApp() {
    return {
        items: [],
        loading: true,

        init() {
            const token = getToken();
            if (!token) { window.location.href = '{{ route("login") }}'; return; }
            this.loadWishlist();
        },

        loadWishlist() {
            this.loading = true;
            const token = getToken();
            axios.get('/api/wishlists', { headers: { Authorization: 'Bearer ' + token } })
                .then(res => this.items = res.data)
                .catch(() => {})
                .finally(() => { this.loading = false; });
        },

        hapusWishlist(item) {
            const token = getToken();
            axios.post('/api/wishlists/toggle', { menu_id: item.menu_id }, {
                headers: { Authorization: 'Bearer ' + token }
            }).then(res => {
                this.items = this.items.filter(i => i.id !== item.id);
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: res.data.message, type: 'success' } }));
            }).catch(() => {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Gagal menghapus', type: 'error' } }));
            });
        }
    }
}
</script>
@endpush
