@extends('layouts.app')

@section('title', 'Lacak Pesanan')

@section('content')
<div x-data="trackingApp()" x-cloak>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Lacak Pesanan</h1>
        <p class="text-gray-500 mt-1">Pantau status pengiriman pesanan kamu</p>
    </div>

    <template x-if="loading">
        <div class="text-center py-20">
            <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-500">Memuat...</p>
        </div>
    </template>

    <template x-if="!loading && orders.length === 0">
        <div class="text-center py-20 text-gray-400">
            <span class="text-8xl block mb-6">🚚</span>
            <p class="text-2xl font-medium text-gray-500 mb-2">Tidak ada pesanan dalam pengiriman</p>
            <a href="{{ route('orders') }}" class="text-blue-600 hover:underline">Lihat semua pesanan</a>
        </div>
    </template>

    <div x-show="!loading && orders.length > 0" class="space-y-6">
        <template x-for="order in orders" :key="order.id">
            <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
                <div class="p-5 border-b bg-gradient-to-r from-blue-50 to-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-bold text-gray-800 text-lg" x-text="order.invoice || ('#' + order.id)"></span>
                            <span class="text-sm text-gray-400 ml-3" x-text="formatDate(order.created_at)"></span>
                        </div>
                        <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                            🚚 Delivery
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mt-2" x-text="'Outlet: ' + (order.outlet?.nama || '-')"></p>
                </div>

                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg"
                                :class="statusIdx(order.status_pesanan) >= 0 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-400'">📝</div>
                            <span class="text-xs mt-1 font-medium"
                                :class="statusIdx(order.status_pesanan) >= 0 ? 'text-blue-600' : 'text-gray-400'">Pesanan</span>
                        </div>
                        <div class="flex-1 h-1 mx-2 rounded"
                            :class="statusIdx(order.status_pesanan) >= 1 ? 'bg-blue-600' : 'bg-gray-200'"></div>
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg"
                                :class="statusIdx(order.status_pesanan) >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-400'">🔄</div>
                            <span class="text-xs mt-1 font-medium"
                                :class="statusIdx(order.status_pesanan) >= 1 ? 'text-blue-600' : 'text-gray-400'">Diproses</span>
                        </div>
                        <div class="flex-1 h-1 mx-2 rounded"
                            :class="statusIdx(order.status_pesanan) >= 2 ? 'bg-blue-600' : 'bg-gray-200'"></div>
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg"
                                :class="statusIdx(order.status_pesanan) >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-400'">🚚</div>
                            <span class="text-xs mt-1 font-medium"
                                :class="statusIdx(order.status_pesanan) >= 2 ? 'text-blue-600' : 'text-gray-400'">Dikirim</span>
                        </div>
                        <div class="flex-1 h-1 mx-2 rounded"
                            :class="statusIdx(order.status_pesanan) >= 3 ? 'bg-blue-600' : 'bg-gray-200'"></div>
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg"
                                :class="statusIdx(order.status_pesanan) >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-400'">✅</div>
                            <span class="text-xs mt-1 font-medium"
                                :class="statusIdx(order.status_pesanan) >= 3 ? 'text-blue-600' : 'text-gray-400'">Selesai</span>
                        </div>
                    </div>

                    <div x-show="order.alamat_pengiriman" class="p-4 bg-blue-50 rounded-xl text-sm">
                        <p class="font-medium text-blue-800 mb-1">📍 Alamat Pengiriman</p>
                        <p class="text-blue-600" x-text="order.alamat_pengiriman"></p>
                        <div class="flex gap-4 mt-2 text-xs text-blue-500">
                            <span x-show="order.nama_kurir" x-text="'Kurir: ' + order.nama_kurir"></span>
                        </div>
                    </div>
                    <div x-show="order.waktu_pengiriman_dijadwalkan" class="p-4 bg-yellow-50 rounded-xl text-sm mt-4">
                        <p class="text-yellow-700 font-medium">📅 Dijadwalkan: <span x-text="formatDate(order.waktu_pengiriman_dijadwalkan)" class="font-bold"></span></p>
                    </div>

                    <div class="flex justify-between font-bold text-lg mt-4 pt-4 border-t border-gray-100">
                        <span class="text-gray-800">Total</span>
                        <span class="text-blue-600" x-text="'Rp ' + order.total_harga.toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection

@push('scripts')
<script>
function trackingApp() {
    return {
        orders: [],
        loading: true,

        init() {
            const token = getToken();
            if (!token) { window.location.href = '{{ route("login") }}'; return; }
            this.loadTracking();
        },

        loadTracking() {
            this.loading = true;
            const token = getToken();
            axios.get('/api/orders?per_page=50', { headers: { Authorization: 'Bearer ' + token } })
                .then(res => {
                    const allOrders = Array.isArray(res.data) ? res.data : (res.data.data || []);
                    this.orders = allOrders
                        .filter(o => o.tipe_pengambilan === 'delivery')
                        .filter(o => !['dibatalkan', 'selesai'].includes(o.status_pesanan));
                })
                .finally(() => { this.loading = false; });
        },

        statusIdx(status) {
            const map = { 'pending': 0, 'diproses': 1, 'diantar': 2, 'selesai': 3 };
            return map[status] ?? -1;
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString('id-ID', {
                year: 'numeric', month: 'short', day: 'numeric',
                hour: '2-digit', minute: '2-digit'
            });
        }
    }
}
</script>
@endpush
