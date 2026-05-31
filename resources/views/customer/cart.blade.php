@extends('layouts.app')

@section('title', 'Keranjang')

@section('content')
<div x-data="cartApp()" x-cloak>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Keranjang Belanja</h1>
            <p class="text-gray-500 mt-1" x-text="cart.length > 0 ? cart.length + ' item ditambahkan' : ''"></p>
        </div>
        <button x-show="cart.length > 0" @click="clearCart()"
            class="text-sm text-red-500 hover:text-red-700 hover:bg-red-50 px-4 py-2 rounded-lg transition font-medium">
            Kosongkan Semua
        </button>
    </div>

    <template x-if="loading">
        <div class="text-center py-20">
            <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-500">Memuat keranjang...</p>
        </div>
    </template>

    <template x-if="!loading && cart.length === 0">
        <div class="text-center py-20 text-gray-400 slide-up">
            <span class="text-8xl block mb-6">🛒</span>
            <p class="text-2xl font-medium text-gray-500 mb-2">Keranjang masih kosong</p>
            <p class="text-gray-400 mb-6">Yuk, pilih menu favorit kamu!</p>
            <a href="{{ route('menu') }}"
                class="inline-block bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition font-medium shadow-md">
                Lihat Menu
            </a>
        </div>
    </template>

    <template x-if="!loading && cart.length > 0">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-4">
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="bg-white rounded-2xl shadow-sm border p-5 flex items-center gap-4 slide-up"
                         :style="'animation-delay: ' + (index * 0.05) + 's'">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden">
                            <template x-if="item.foto_menu">
                                <img :src="'/storage/' + item.foto_menu" :alt="item.nama_menu" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!item.foto_menu">
                                <span class="text-lg text-gray-300 font-bold" x-text="item.nama_menu?.charAt(0) || '☕'"></span>
                            </template>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-800 truncate" x-text="item.nama_menu"></h3>
                            <p x-show="item.nama_varian" class="text-sm font-medium text-blue-600" x-text="item.nama_varian"></p>
                            <template x-for="opt in (item.selected_options || [])" :key="opt.item_id">
                                <span class="inline-block text-xs bg-gray-100 text-gray-600 rounded px-1.5 py-0.5 mr-1 mt-0.5">
                                    <span x-text="opt.group_name" class="font-medium"></span>: <span x-text="opt.item_name"></span>
                                </span>
                            </template>
                            <p class="text-blue-700 font-bold text-sm mt-1" x-text="'Rp' + item.harga.toLocaleString('id-ID') + '/item'"></p>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-2 py-1">
                                <button @click="updateQty(item, item.jumlah - 1)"
                                    class="w-8 h-8 rounded-lg bg-white hover:bg-gray-100 transition text-gray-600 font-medium shadow-sm">−</button>
                                <span class="w-8 text-center font-semibold text-gray-800" x-text="item.jumlah"></span>
                                <button @click="updateQty(item, item.jumlah + 1)"
                                    class="w-8 h-8 rounded-lg bg-white hover:bg-gray-100 transition text-gray-600 font-medium shadow-sm">+</button>
                            </div>
                            <button @click="openEditModal(item)" class="text-xs text-blue-600 hover:text-blue-800 hover:underline transition">
                                ✏️ Edit
                            </button>
                        </div>
                        <p class="font-bold text-gray-800 w-24 text-right hidden sm:block" x-text="'Rp' + item.subtotal.toLocaleString('id-ID')"></p>
                        <button @click="removeItem(item)"
                            class="text-gray-300 hover:text-red-500 transition p-1 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border p-6 sticky top-24">
                    <h3 class="font-bold text-lg text-gray-800 mb-6">Ringkasan</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-600">
                            <span>Total Item</span>
                            <span class="font-medium" x-text="cart.length + ' item'"></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-medium" x-text="'Rp' + total.toLocaleString('id-ID')"></span>
                        </div>
                    </div>
                    <div class="flex justify-between text-xl font-bold mt-6 pt-6 border-t border-gray-100">
                        <span>Total</span>
                        <span class="text-blue-700" x-text="'Rp' + total.toLocaleString('id-ID')"></span>
                    </div>
                    <a href="{{ route('checkout') }}"
                        class="block w-full mt-6 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3.5 rounded-xl text-center hover:from-blue-700 hover:to-indigo-700 transition font-medium shadow-md active:scale-[0.98]">
                    Lanjut ke Checkout
                    </a>
                    <a href="{{ route('menu') }}"
                        class="block w-full mt-3 text-center text-sm text-gray-500 hover:text-blue-600 transition py-2">
                        + Tambah Menu Lagi
                    </a>
                </div>
            </div>
        </div>
    </template>

    {{-- Edit Item Modal --}}
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[70] flex items-center justify-center p-4"
        @click.self="closeEditModal()">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="p-5 border-b flex items-center justify-between sticky top-0 bg-white z-10">
                <div>
                    <h3 class="text-lg font-bold text-gray-800" x-text="editItem?.nama_menu"></h3>
                    <p class="text-sm text-gray-500" x-text="'Rp' + (editHargaDasar || 0).toLocaleString('id-ID')"></p>
                </div>
                <button @click="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>

            <div class="p-5 space-y-6">
                <template x-if="editItem?.variants?.length > 1 || (editItem?.variants?.length === 1 && editItem.variants[0].nama !== 'Regular')">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Varian <span class="text-gray-400 font-normal">(Suhu/Ukuran)</span></h4>
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="v in editItem.variants" :key="v.id">
                                <button @click="editSelectedVariant = v"
                                    class="p-3 rounded-xl border text-left transition"
                                    :class="editSelectedVariant?.id === v.id ? 'border-blue-600 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200 hover:border-blue-300'">
                                    <span class="block text-sm font-medium text-gray-800" x-text="v.nama"></span>
                                    <span x-show="v.harga_tambahan > 0" class="text-xs text-blue-600" x-text="'+Rp' + v.harga_tambahan.toLocaleString('id-ID')"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-for="group in (editItem?.option_groups || [])" :key="group.id">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3" x-text="group.nama"></h4>
                        <template x-if="group.tipe === 'single'">
                            <div class="flex flex-wrap gap-2">
                                <template x-for="opt in (group.items || [])" :key="opt.id">
                                    <button @click="pilihEditOption(group, opt)"
                                        class="px-4 py-2.5 rounded-xl border text-sm transition"
                                        :class="isEditOptionSelected(group.id, opt.id) ? 'border-blue-600 bg-blue-50 ring-2 ring-blue-200 text-blue-700 font-medium' : 'border-gray-200 hover:border-blue-300 text-gray-700'">
                                        <span x-text="opt.nama"></span>
                                        <span x-show="opt.harga_tambahan > 0" class="text-blue-600 ml-1" x-text="'+Rp' + opt.harga_tambahan.toLocaleString('id-ID')"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                        <template x-if="group.tipe === 'multiple'">
                            <div class="flex flex-wrap gap-2">
                                <template x-for="opt in (group.items || [])" :key="opt.id">
                                    <button @click="toggleEditAddon(group, opt)"
                                        class="px-4 py-2.5 rounded-xl border text-sm transition"
                                        :class="isEditOptionSelected(group.id, opt.id) ? 'border-green-500 bg-green-50 ring-2 ring-green-200 text-green-700 font-medium' : 'border-gray-200 hover:border-green-300 text-gray-700'">
                                        <span x-text="opt.nama"></span>
                                        <span x-show="opt.harga_tambahan > 0" class="text-green-600 ml-1" x-text="'+Rp' + opt.harga_tambahan.toLocaleString('id-ID')"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Jumlah --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Jumlah</h4>
                    <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-2 w-fit">
                        <button @click="if (editJumlah > 1) editJumlah--"
                            class="w-10 h-10 rounded-lg bg-white hover:bg-gray-100 transition text-gray-600 font-medium shadow-sm text-lg">−</button>
                        <span class="w-10 text-center font-bold text-gray-800 text-lg" x-text="editJumlah"></span>
                        <button @click="editJumlah++"
                            class="w-10 h-10 rounded-lg bg-white hover:bg-gray-100 transition text-gray-600 font-medium shadow-sm text-lg">+</button>
                    </div>
                </div>

                {{-- Ringkasan Harga --}}
                <div class="bg-gray-50 rounded-xl p-4 space-y-1.5 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Harga dasar</span>
                        <span x-text="'Rp' + (editHargaDasar || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <div x-show="editSelectedVariant?.harga_tambahan > 0" class="flex justify-between text-gray-600">
                        <span>Varian <span x-text="editSelectedVariant?.nama"></span></span>
                        <span x-text="'+Rp' + (editSelectedVariant?.harga_tambahan || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <template x-for="opt in editSelectedOptions" :key="opt.item_id">
                        <div x-show="opt.harga_tambahan > 0" class="flex justify-between text-gray-600">
                            <span x-text="opt.item_name"></span>
                            <span x-text="'+Rp' + opt.harga_tambahan.toLocaleString('id-ID')"></span>
                        </div>
                    </template>
                    <div class="flex justify-between font-bold text-gray-800 pt-2 border-t border-gray-200">
                        <span>Total per item</span>
                        <span class="text-blue-700" x-text="'Rp' + editCalculatedPrice.toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </div>

            <div class="p-5 border-t sticky bottom-0 bg-white flex gap-3">
                <button @click="closeEditModal()"
                    class="flex-1 py-3 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition font-medium">
                    Batal
                </button>
                <button @click="saveEditItem()" :disabled="savingEdit"
                    class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition font-medium shadow-md disabled:opacity-50 active:scale-[0.98]">
                    <span x-show="!savingEdit">Simpan</span>
                    <span x-show="savingEdit">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function cartApp() {
    return {
        cart: [],
        total: 0,
        loading: true,

        // Edit modal state
        showEditModal: false,
        editItem: null,
        editSelectedVariant: null,
        editSelectedOptions: [],
        editJumlah: 1,
        editHargaDasar: 0,
        savingEdit: false,

        init() {
            const token = getToken();
            if (!token) { window.location.href = '{{ route("login") }}'; return; }
            this.loadCart();
        },

        loadCart() {
            this.loading = true;
            const token = getToken();
            axios.get('/api/cart', { headers: { Authorization: 'Bearer ' + token } })
                .then(res => {
                    this.cart = Object.values(res.data.cart);
                    this.total = res.data.total;
                })
                .catch(() => {
                    const t = getToken();
                    if (!t) window.location.href = '{{ route("login") }}';
                })
                .finally(() => { this.loading = false; });
        },

        updateQty(item, qty) {
            if (qty < 0) return;
            const token = getToken();
            axios.put('/api/cart/update', {
                menu_id: item.menu_id,
                menu_variant_id: item.menu_variant_id,
                selected_options: (item.selected_options || []).map(o => ({ group_id: o.group_id, item_id: o.item_id })),
                jumlah: qty,
            }, {
                headers: { Authorization: 'Bearer ' + token }
            }).then(() => this.loadCart());
        },

        removeItem(item) {
            const token = getToken();
            axios.delete('/api/cart/remove/' + item.id, {
                headers: { Authorization: 'Bearer ' + token }
            }).then(() => {
                this.loadCart();
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Item dihapus dari keranjang', type: 'success' } }));
            });
        },

        clearCart() {
            const token = getToken();
            axios.post('/api/cart/clear', {}, {
                headers: { Authorization: 'Bearer ' + token }
            }).then(() => {
                this.loadCart();
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Keranjang dikosongkan', type: 'success' } }));
            });
        },

        // --- Edit Modal ---
        openEditModal(item) {
            this.editItem = item;
            this.editHargaDasar = item.harga_dasar || item.harga;
            this.editJumlah = item.jumlah;
            this.editSelectedVariant = null;
            this.editSelectedOptions = [];

            // Fetch menu details (variants + option groups) from API
            const token = getToken();
            axios.get('/api/menu/' + item.menu_id, {
                headers: { Authorization: 'Bearer ' + token }
            }).then(res => {
                const menu = res.data;
                this.editItem = { ...item, variants: menu.variants || [], option_groups: menu.option_groups || [] };

                // Pre-select current variant
                if (item.menu_variant_id && menu.variants) {
                    this.editSelectedVariant = menu.variants.find(v => v.id === item.menu_variant_id) || null;
                }

                // Pre-select current options
                this.editSelectedOptions = [];
                if (item.selected_options) {
                    this.editSelectedOptions = item.selected_options.map(o => ({
                        group_id: o.group_id,
                        group_name: o.group_name,
                        item_id: o.item_id,
                        item_name: o.item_name,
                        harga_tambahan: o.harga_tambahan,
                    }));
                }

                this.showEditModal = true;
            }).catch(() => {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Gagal memuat detail menu', type: 'error' } }));
            });
        },

        closeEditModal() {
            this.showEditModal = false;
            this.editItem = null;
            this.editSelectedVariant = null;
            this.editSelectedOptions = [];
            this.editJumlah = 1;
        },

        pilihEditOption(group, opt) {
            this.editSelectedOptions = this.editSelectedOptions.filter(o => o.group_id !== group.id);
            this.editSelectedOptions.push({
                group_id: group.id,
                group_name: group.nama,
                item_id: opt.id,
                item_name: opt.nama,
                harga_tambahan: opt.harga_tambahan || 0,
            });
        },

        toggleEditAddon(group, opt) {
            const idx = this.editSelectedOptions.findIndex(o => o.item_id === opt.id);
            if (idx > -1) {
                this.editSelectedOptions.splice(idx, 1);
            } else {
                this.editSelectedOptions.push({
                    group_id: group.id,
                    group_name: group.nama,
                    item_id: opt.id,
                    item_name: opt.nama,
                    harga_tambahan: opt.harga_tambahan || 0,
                });
            }
        },

        isEditOptionSelected(groupId, itemId) {
            return this.editSelectedOptions.some(o => o.group_id === groupId && o.item_id === itemId);
        },

        get editCalculatedPrice() {
            const item = this.editItem;
            if (!item) return 0;
            let total = this.editHargaDasar;
            if (this.editSelectedVariant) {
                total += this.editSelectedVariant.harga_tambahan || 0;
            }
            this.editSelectedOptions.forEach(opt => {
                total += opt.harga_tambahan || 0;
            });
            return total;
        },

        saveEditItem() {
            const token = getToken();
            if (!token) return;

            this.savingEdit = true;
            const payload = {
                id: this.editItem.id,
                menu_id: this.editItem.menu_id,
                menu_variant_id: this.editSelectedVariant?.id || null,
                selected_options: this.editSelectedOptions.map(o => ({
                    group_id: o.group_id,
                    item_id: o.item_id,
                })),
                jumlah: this.editJumlah,
            };

            axios.put('/api/cart/update', payload, {
                headers: { Authorization: 'Bearer ' + token }
            }).then(() => {
                this.closeEditModal();
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Item diperbarui', type: 'success' } }));
                this.loadCart();
            }).catch(err => {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: err.response?.data?.message || 'Gagal memperbarui item', type: 'error' } }));
            }).finally(() => {
                this.savingEdit = false;
            });
        },
    }
}
</script>
@endpush