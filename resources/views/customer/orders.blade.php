@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div x-data="orderApp()" x-cloak>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Pesanan Saya</h1>
        <p class="text-gray-500 mt-1">Riwayat dan status pesanan kamu</p>
    </div>

    <div x-show="!loading" class="mb-6 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
            <input type="text" x-model="search" @input.debounce="loadOrders()" placeholder="Cari invoice atau outlet..."
                class="w-full pl-10 border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-sm">
        </div>
        <select x-model="filterStatus" @change="loadOrders()"
            class="border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 bg-white text-sm">
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="diproses">Diproses</option>
            <option value="diantar">Diantar</option>
            <option value="selesai">Selesai</option>
            <option value="dibatalkan">Dibatalkan</option>
        </select>
    </div>

    <template x-if="loading">
        <div class="text-center py-20">
            <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-500">Memuat pesanan...</p>
        </div>
    </template>

    <template x-if="!loading && orders.data.length === 0">
        <div class="text-center py-20 text-gray-400 slide-up">
            <span class="text-8xl block mb-6">📋</span>
            <p class="text-2xl font-medium text-gray-500 mb-2">Belum ada pesanan</p>
            <p class="text-gray-400 mb-6">Ayo pesan menu favorit kamu sekarang!</p>
            <a href="{{ route('menu') }}"
                class="inline-block bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition font-medium shadow-md">
                Pesan Sekarang
            </a>
        </div>
    </template>

    <div x-show="!loading && orders.data && orders.data.length > 0" class="space-y-4">
        <template x-for="(order, index) in orders.data" :key="order.id">
            <div class="bg-white rounded-2xl shadow-sm border overflow-hidden slide-up"
                 :style="'animation-delay: ' + (index * 0.05) + 's'">
                <div class="p-5 border-b bg-gradient-to-r from-gray-50 to-white flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                    <div>
                        <span class="font-bold text-gray-800 text-lg" x-text="order.invoice || ('#' + order.id)"></span>
                        <span class="text-sm text-gray-400 ml-3" x-text="formatDate(order.created_at)"></span>
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <span class="px-3 py-1.5 rounded-full text-xs font-medium"
                            :class="statusPembayaranClass(order.status_pembayaran)"
                            x-text="statusLabel(order.status_pembayaran)"></span>
                        <span class="px-3 py-1.5 rounded-full text-xs font-medium"
                            :class="statusPesananClass(order.status_pesanan)"
                            x-text="statusPesananLabel(order.status_pesanan)"></span>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex flex-col sm:flex-row sm:justify-between text-sm text-gray-500 mb-4 gap-1">
                        <div class="flex flex-wrap gap-x-4 gap-y-1">
                            <span x-show="order.outlet">
                                📍 <span class="font-medium text-gray-700" x-text="order.outlet.nama"></span>
                            </span>
                            <span x-show="order.tipe_pengambilan === 'pickup'">
                                🛵 <span class="font-medium text-blue-600">Pickup</span>
                            </span>
                            <span x-show="order.tipe_pengambilan === 'ditempat'">
                                🪑 Meja: <span class="font-medium text-gray-700" x-text="order.no_meja || '-'"></span>
                            </span>
                            <span x-show="order.tipe_pengambilan === 'delivery'">
                                🚚 <span class="font-medium text-blue-600">Delivery</span>
                            </span>
                        </div>
                        <span class="px-2 py-0.5 bg-gray-50 rounded text-xs" x-text="order.metode_pembayaran === 'midtrans' ? 'Midtrans' : 'Bayar di Kasir'"></span>
                    </div>

                    <div x-show="order.tipe_pengambilan === 'delivery'" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm">
                        <p class="text-blue-700 font-medium mb-1">🚚 Alamat Pengiriman:</p>
                        <p class="text-blue-600" x-text="order.alamat_pengiriman"></p>
                        <div class="flex gap-4 mt-1 text-xs text-blue-500">
                            <span x-show="order.ongkir > 0" x-text="'Ongkir: Rp ' + order.ongkir.toLocaleString('id-ID')"></span>
                            <span x-show="order.nama_kurir" x-text="'Kurir: ' + order.nama_kurir"></span>
                        </div>
                    </div>
                    <div x-show="order.waktu_pengiriman_dijadwalkan" class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-xl text-sm">
                        <p class="text-yellow-700 font-medium">📅 Dijadwalkan: <span x-text="formatDate(order.waktu_pengiriman_dijadwalkan)" class="font-bold"></span></p>
                    </div>
                    <div class="space-y-2">
                        <template x-for="detail in order.detail_transaksis" :key="detail.id">
                            <div class="flex justify-between py-1.5 text-sm border-b border-gray-50 last:border-0">
                                <span class="text-gray-700">
                                    <span x-text="detail.menu?.nama_menu"></span>
                                    <span x-show="detail.variant" class="text-blue-600 text-xs" x-text="'(' + detail.variant.nama + ')'"></span>
                                    <div class="flex flex-wrap gap-1 mt-0.5">
                                        <template x-for="opt in (detail.selected_options || [])" :key="opt.item_id">
                                            <span class="inline-block text-xs bg-gray-100 text-gray-600 rounded px-1.5 py-0.5">
                                                <span x-text="opt.group_name" class="font-medium"></span>: <span x-text="opt.item_name"></span>
                                            </span>
                                        </template>
                                    </div>
                                    <span x-text="' × ' + detail.jumlah"></span>
                                </span>
                                <span class="font-medium text-gray-700" x-text="'Rp' + detail.subtotal.toLocaleString('id-ID')"></span>
                            </div>
                        </template>
                    </div>
                    <div class="flex justify-between font-bold text-lg mt-4 pt-4 border-t border-gray-100">
                        <span class="text-gray-800">Total</span>
                        <span class="text-blue-600 font-bold" x-text="'Rp' + order.total_harga.toLocaleString('id-ID')"></span>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <button x-show="order.status_pembayaran === 'belum_bayar'"
                            @click="bayarSekarang(order)"
                            :disabled="payToken === order.id"
                            class="flex-1 text-center text-sm font-medium py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                            <span x-show="payToken !== order.id">💳 Bayar Sekarang</span>
                            <span x-show="payToken === order.id">Memuat pembayaran...</span>
                        </button>
                        <button x-show="order.status_pesanan === 'pending'"
                            @click="batalkanPesanan(order.id, index)"
                            :disabled="cancelling === order.id"
                            class="flex-1 text-center text-sm font-medium py-2.5 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="cancelling !== order.id">✕ Batalkan</span>
                            <span x-show="cancelling === order.id">Membatalkan...</span>
                        </button>
                        <button x-show="order.status_pesanan === 'selesai' || order.status_pesanan === 'dibatalkan'"
                            @click="pesanLagi(order.id, index)"
                            :disabled="reordering === order.id"
                            class="flex-1 text-center text-sm font-medium py-2.5 rounded-xl border border-blue-200 text-blue-600 hover:bg-blue-50 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="reordering !== order.id">🔄 Pesan Lagi</span>
                            <span x-show="reordering === order.id">Menambahkan...</span>
                        </button>
                        <button x-show="order.status_pesanan === 'selesai'"
                            @click="bukaRatingModal(order)"
                            class="flex-1 text-center text-sm font-medium py-2.5 rounded-xl border border-yellow-200 text-yellow-700 hover:bg-yellow-50 transition">
                            ⭐ Beri Rating
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <div x-show="orders.last_page > 1" class="flex justify-center gap-2 pt-4">
            <button @click="goToPage(orders.current_page - 1)" :disabled="orders.current_page <= 1"
                class="px-4 py-2 rounded-xl text-sm font-medium border transition"
                :class="orders.current_page <= 1 ? 'border-gray-100 text-gray-300 cursor-not-allowed' : 'border-gray-200 text-gray-600 hover:bg-gray-50'">
                Sebelumnya
            </button>
            <template x-for="page in orders.last_page" :key="page">
                <button @click="goToPage(page)"
                    class="px-4 py-2 rounded-xl text-sm font-medium border transition"
                    :class="page === orders.current_page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                    x-text="page">
                </button>
            </template>
            <button @click="goToPage(orders.current_page + 1)" :disabled="orders.current_page >= orders.last_page"
                class="px-4 py-2 rounded-xl text-sm font-medium border transition"
                :class="orders.current_page >= orders.last_page ? 'border-gray-100 text-gray-300 cursor-not-allowed' : 'border-gray-200 text-gray-600 hover:bg-gray-50'">
                Selanjutnya
            </button>
        </div>
    </div>

    {{-- Modal Rating --}}
    <div x-show="showRatingModal" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center p-4"
        @click.self="showRatingModal = false">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-gray-800">Beri Rating</h3>
                <button @click="showRatingModal = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <div class="space-y-5">
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-3" x-text="ratingOrder?.invoice || '#' + ratingOrder?.id"></p>
                    <div class="flex justify-center gap-2">
                        <template x-for="i in 5" :key="i">
                            <button @click="ratingValue = i" type="button"
                                class="text-3xl transition hover:scale-110"
                                :class="i <= ratingValue ? 'opacity-100' : 'opacity-30'">
                                <span x-text="i <= ratingValue ? '⭐' : '☆'"></span>
                            </button>
                        </template>
                    </div>
                    <p class="text-sm text-gray-400 mt-2" x-text="ratingLabel"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Ulasan (opsional)</label>
                    <textarea x-model="ratingReview" rows="3" maxlength="1000" placeholder="Ceritakan pengalaman kamu..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 bg-white resize-none"></textarea>
                    <p class="text-xs text-gray-400 text-right mt-1" x-text="ratingReview.length + '/1000'"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Pilih menu yang ingin dinilai</label>
                    <div class="space-y-2 max-h-40 overflow-y-auto">
                        <template x-for="detail in (ratingOrder?.detail_transaksis || [])" :key="detail.id">
                            <label class="flex items-center gap-3 p-2 rounded-lg border cursor-pointer transition"
                                :class="ratingMenuIds.includes(detail.menu_id) ? 'border-yellow-400 bg-yellow-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" :value="detail.menu_id" x-model="ratingMenuIds"
                                    class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-500">
                                <span class="text-sm text-gray-700" x-text="detail.menu?.nama_menu"></span>
                            </label>
                        </template>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button @click="kirimRating()" :disabled="ratingLoading || ratingMenuIds.length === 0"
                        class="flex-1 bg-gradient-to-r from-yellow-500 to-orange-500 text-white py-3 rounded-xl hover:from-yellow-600 hover:to-orange-600 transition font-medium disabled:opacity-50 shadow-sm">
                        <span x-show="!ratingLoading">Kirim Rating</span>
                        <span x-show="ratingLoading">Mengirim...</span>
                    </button>
                    <button @click="showRatingModal = false"
                        class="px-6 py-3 border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
function orderApp() {
    return {
        orders: { data: [], current_page: 1, last_page: 1 },
        loading: true,
        search: '',
        filterStatus: '',

        init() {
            const token = getToken();
            if (!token) { window.location.href = '{{ route("login") }}'; return; }
            this.loadOrders();
        },

        loadOrders() {
            this.loading = true;
            const token = getToken();
            const params = { page: this.orders.current_page };
            if (this.search) params.search = this.search;
            if (this.filterStatus) params.status = this.filterStatus;
            axios.get('/api/orders', { params, headers: { Authorization: 'Bearer ' + token } })
                .then(res => this.orders = res.data)
                .catch(() => {
                    if (!getToken()) window.location.href = '{{ route("login") }}';
                })
                .finally(() => { this.loading = false; });
        },

        goToPage(page) {
            this.orders.current_page = page;
            this.loadOrders();
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString('id-ID', {
                year: 'numeric', month: 'short', day: 'numeric',
                hour: '2-digit', minute: '2-digit'
            });
        },

        statusLabel(status) {
            const map = { 'belum_bayar': 'Belum Bayar', 'lunas': 'Lunas', 'expired': 'Expired', 'gagal': 'Gagal' };
            return map[status] || status;
        },

        statusPembayaranClass(status) {
            const map = { 'belum_bayar': 'bg-yellow-50 text-yellow-700 border border-yellow-200', 'lunas': 'bg-green-50 text-green-700 border border-green-200', 'expired': 'bg-red-50 text-red-700 border border-red-200', 'gagal': 'bg-red-50 text-red-700 border border-red-200' };
            return map[status] || 'bg-gray-50 text-gray-600 border border-gray-200';
        },

        statusPesananLabel(status) {
            const map = { 'pending': 'Pending', 'diproses': 'Diproses', 'diantar': 'Diantar', 'selesai': 'Selesai', 'dibatalkan': 'Dibatalkan' };
            return map[status] || status;
        },

        statusPesananClass(status) {
            const map = { 'pending': 'bg-yellow-50 text-yellow-700 border border-yellow-200', 'diproses': 'bg-blue-50 text-blue-700 border border-blue-200', 'diantar': 'bg-blue-50 text-blue-700 border border-blue-200', 'selesai': 'bg-green-50 text-green-700 border border-green-200', 'dibatalkan': 'bg-red-50 text-red-700 border border-red-200' };
            return map[status] || 'bg-gray-50';
        },

        payToken: null,
        cancelling: null,
        reordering: null,

        showRatingModal: false,
        ratingOrder: null,
        ratingValue: 0,
        ratingReview: '',
        ratingMenuIds: [],
        ratingLoading: false,

        get ratingLabel() {
            const labels = ['', 'Buruk', 'Kurang', 'Cukup', 'Baik', 'Sangat Baik'];
            return labels[this.ratingValue] || '';
        },

        bukaRatingModal(order) {
            this.ratingOrder = order;
            this.ratingValue = 0;
            this.ratingReview = '';
            this.ratingMenuIds = [];
            this.showRatingModal = true;
        },

        kirimRating() {
            if (this.ratingMenuIds.length === 0) return;
            this.ratingLoading = true;
            const token = getToken();
            const promises = this.ratingMenuIds.map(menu_id => {
                return axios.post('/api/ratings', {
                    menu_id,
                    transaksi_id: this.ratingOrder.id,
                    rating: this.ratingValue,
                    review: this.ratingReview,
                }, { headers: { Authorization: 'Bearer ' + token } });
            });
            Promise.all(promises)
                .then(() => {
                    this.showRatingModal = false;
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Rating berhasil dikirim! Terima kasih atas ulasannya 🙏', type: 'success' } }));
                })
                .catch(err => {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: err.response?.data?.message || 'Gagal mengirim rating', type: 'error' } }));
                })
                .finally(() => { this.ratingLoading = false; });
        },

        bayarSekarang(order) {
            this.payToken = order.id;
            const pay = (token) => {
                snap.pay(token, {
                    onSuccess: () => {
                        fetch('/orders/confirm/' + order.id, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
                        }).then(() => { this.loadOrders(); }).catch(() => { this.loadOrders(); });
                    },
                    onPending: () => { this.payToken = null; },
                    onError: () => {
                        this.payToken = null;
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Pembayaran dibatalkan', type: 'error' } }));
                    }
                });
            };
            if (order.midtrans_snap_token) {
                pay(order.midtrans_snap_token);
            } else {
                const token = getToken();
                axios.post('/api/orders/' + order.id + '/pay-now', {}, {
                    headers: { Authorization: 'Bearer ' + token }
                }).then(res => {
                    order.midtrans_snap_token = res.data.snap_token;
                    pay(res.data.snap_token);
                }).catch(err => {
                    this.payToken = null;
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: err.response?.data?.message || 'Gagal memproses pembayaran', type: 'error' } }));
                });
            }
        },

        batalkanPesanan(orderId, index) {
            if (!confirm('Yakin ingin membatalkan pesanan ini?')) return;
            this.cancelling = orderId;
            const token = getToken();
            axios.post('/api/orders/' + orderId + '/cancel', {}, {
                headers: { Authorization: 'Bearer ' + token }
            }).then(res => {
                this.orders.data[index].status_pesanan = 'dibatalkan';
                if (res.data.transaksi.status_pembayaran === 'gagal') {
                    this.orders.data[index].status_pembayaran = 'gagal';
                }
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Pesanan dibatalkan', type: 'success' } }));
            }).catch(err => {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: err.response?.data?.message || 'Gagal membatalkan', type: 'error' } }));
            }).finally(() => {
                this.cancelling = null;
            });
        },

        pesanLagi(orderId, index) {
            this.reordering = orderId;
            const token = getToken();
            axios.post('/api/orders/' + orderId + '/reorder', {}, {
                headers: { Authorization: 'Bearer ' + token }
            }).then(res => {
                window.dispatchEvent(new CustomEvent('cart-updated'));
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Item ditambahkan ke keranjang!', type: 'success' } }));
            }).catch(err => {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: err.response?.data?.message || 'Gagal memesan ulang', type: 'error' } }));
            }).finally(() => {
                this.reordering = null;
            });
        }
    }
}
</script>
@endpush
