@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div x-data="checkoutApp()" x-cloak>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Checkout</h1>
        <p class="text-gray-500 mt-1">Konfirmasi pesanan dan pilih metode pembayaran</p>
    </div>

    <template x-if="!outletDipilih">
        <div class="text-center py-20 text-gray-400">
            <span class="text-8xl block mb-4">📍</span>
            <p class="text-xl text-gray-500 mb-2">Pilih outlet terlebih dahulu</p>
            <p class="text-gray-400 mb-6">Silakan pilih outlet Tens Coffee sebelum checkout</p>
            <a href="{{ route('menu') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-xl hover:bg-blue-700 transition font-medium">Pilih Outlet</a>
        </div>
    </template>

    <template x-if="loading">
        <div class="text-center py-20">
            <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-500">Memuat keranjang...</p>
        </div>
    </template>

    <div x-show="!loading && outletDipilih" class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border p-6">
                <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                    <span>📍</span> Outlet
                </h3>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center flex-shrink-0 overflow-hidden"><img src="/img/logo_tens2.jpg" alt="Tens Coffee" class="w-full h-full object-contain p-1"></div>
                    <div>
                        <p class="font-semibold text-gray-800" x-text="outletDipilih.nama"></p>
                        <p class="text-sm text-gray-500" x-text="outletDipilih.alamat"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border p-6">
                <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                    <span>📋</span> Detail Pesanan
                </h3>
                <template x-if="cart.length === 0">
                    <div class="text-center py-8 text-gray-400">
                        <p class="mb-3">Keranjang kosong</p>
                        <a href="{{ route('menu') }}" class="text-blue-600 hover:underline font-medium">Lihat menu</a>
                    </div>
                </template>
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="flex justify-between py-3 border-b border-gray-50 last:border-0">
                        <div>
                            <span class="text-gray-800 font-medium" x-text="item.nama_menu"></span>
                            <span x-show="item.nama_varian" class="text-blue-600 text-xs ml-1" x-text="'(' + item.nama_varian + ')'"></span>
                            <div class="flex flex-wrap gap-1 mt-1">
                                <template x-for="opt in (item.selected_options || [])" :key="opt.item_id">
                                    <span class="inline-block text-xs bg-gray-100 text-gray-600 rounded px-1.5 py-0.5">
                                        <span x-text="opt.group_name" class="font-medium"></span>: <span x-text="opt.item_name"></span>
                                    </span>
                                </template>
                            </div>
                            <span class="text-gray-400 text-xs" x-text="'× ' + item.jumlah + ' item'"></span>
                        </div>
                        <span class="font-medium text-gray-800" x-text="'Rp ' + item.subtotal.toLocaleString('id-ID')"></span>
                    </div>
                </template>
                <div class="flex justify-between text-xl font-bold mt-4 pt-4 border-t border-gray-100">
                    <span>Total Menu</span>
                    <span class="text-blue-700" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border p-6">
                <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                    <span>🎫</span> Voucher Diskon
                </h3>
                <div class="flex gap-2">
                    <input type="text" x-model="kodeVoucher" placeholder="Masukkan kode voucher"
                        class="flex-1 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <button @click="applyVoucher()" :disabled="!kodeVoucher || cekVoucherLoading"
                        class="bg-blue-600 text-white px-5 py-3 rounded-xl hover:bg-blue-700 transition font-medium disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed">
                        <span x-show="!cekVoucherLoading">Pakai</span>
                        <span x-show="cekVoucherLoading" class="inline-block w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    </button>
                </div>
                <template x-if="voucherTerpakai">
                    <div class="mt-3 flex items-center justify-between bg-green-50 border border-green-200 rounded-xl px-4 py-3">
                        <div>
                            <span class="text-sm font-medium text-green-700" x-text="voucherTerpakai.nama"></span>
                            <p class="text-xs text-green-600 mt-0.5">Diskon: -Rp <span x-text="voucherTerpakai.diskon.toLocaleString('id-ID')"></span></p>
                        </div>
                        <button @click="hapusVoucher()" class="text-red-500 hover:text-red-700 text-sm font-medium">Hapus</button>
                    </div>
                </template>
                <p x-show="voucherError" class="text-red-500 text-sm mt-2" x-text="voucherError"></p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border p-6">
                <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                    <span>🛵</span> Metode Pengambilan
                </h3>
                <div class="space-y-3 mb-4">
                    <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition"
                        :class="tipePengambilan === 'ditempat' ? 'border-blue-600 bg-blue-50' : 'hover:bg-gray-50'">
                        <input type="radio" value="ditempat" x-model="tipePengambilan" class="accent-blue-600">
                        <div>
                            <span class="font-medium text-gray-800">Makan di Tempat</span>
                            <p class="text-xs text-gray-400">Duduk dan nikmati di outlet</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition"
                        :class="tipePengambilan === 'pickup' ? 'border-blue-600 bg-blue-50' : 'hover:bg-gray-50'">
                        <input type="radio" value="pickup" x-model="tipePengambilan" class="accent-blue-600">
                        <div>
                            <span class="font-medium text-gray-800">Pickup</span>
                            <p class="text-xs text-gray-400">Pesan sekarang, ambil nanti</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition"
                        :class="tipePengambilan === 'delivery' ? 'border-blue-600 bg-blue-50' : 'hover:bg-gray-50'">
                        <input type="radio" value="delivery" x-model="tipePengambilan" class="accent-blue-600">
                        <div>
                            <span class="font-medium text-gray-800">Delivery</span>
                            <p class="text-xs text-gray-400">Pesan antar ke lokasi Anda</p>
                        </div>
                    </label>
                </div>

                <div x-show="tipePengambilan === 'ditempat'">
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Nomor Meja</label>
                    <input type="text" x-model="noMeja" placeholder="Contoh: A1"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <p class="text-xs text-gray-400 mt-1.5">Masukkan nomor meja tempat Anda duduk</p>
                </div>

                <div x-show="tipePengambilan === 'pickup'" class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <p class="text-sm text-blue-700 font-medium">🛵 Pesanan akan siap diambil di outlet <span x-text="outletDipilih.nama" class="font-bold"></span></p>
                    <p class="text-xs text-blue-500 mt-1">Kami akan memberitahu status pesanan Anda</p>
                </div>

                <div x-show="tipePengambilan === 'delivery'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">Alamat Lengkap</label>
                        <textarea x-model="alamatPengiriman" rows="3" placeholder="Jalan, gedung, nomor, RT/RW, catatan untuk kurir"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">Pilih Lokasi di Peta</label>
                        <div id="map-picker" class="h-64 rounded-xl border border-gray-200 z-10"></div>
                        <p class="text-xs text-gray-400 mt-1.5">Klik peta untuk menentukan lokasi pengiriman</p>
                        <p x-show="latPengiriman" class="text-xs text-green-600 mt-1">✅ Lokasi sudah dipilih</p>
                    </div>

                    <div x-show="ongkirLoading" class="text-center py-2">
                        <div class="w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto"></div>
                    </div>
                    <div x-show="ongkir !== null && !ongkirLoading" class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-blue-700">Ongkos Kirim</span>
                            <span class="font-bold text-blue-700" x-text="ongkir > 0 ? 'Rp ' + ongkir.toLocaleString('id-ID') : 'GRATIS'"></span>
                        </div>
                        <p x-show="total >= 50000" class="text-xs text-green-600 mt-1">🎉 Gratis ongkir untuk minimal belanja Rp50.000</p>
                    </div>
                </div>

        <div class="mt-4" x-show="tipePengambilan !== 'ditempat'">
            <label class="block text-sm font-medium text-gray-600 mb-1.5">Jadwal Pesanan</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition"
                                :class="jadwalPengiriman === 'sekarang' ? 'border-blue-600 bg-blue-50' : 'hover:bg-gray-50'">
                                <input type="radio" value="sekarang" x-model="jadwalPengiriman" class="accent-blue-600">
                                <span class="text-sm text-gray-700"
                                    x-text="tipePengambilan === 'delivery' ? 'Antar Sekarang' : tipePengambilan === 'pickup' ? 'Ambil Sekarang' : 'Makan Sekarang'">
                                </span>
                            </label>
                            <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition"
                                :class="jadwalPengiriman === 'dijadwalkan' ? 'border-blue-600 bg-blue-50' : 'hover:bg-gray-50'">
                                <input type="radio" value="dijadwalkan" x-model="jadwalPengiriman" class="accent-blue-600">
                                <span class="text-sm text-gray-700">Jadwalkan</span>
                            </label>
                        </div>
                    <div x-show="jadwalPengiriman === 'dijadwalkan'" class="mt-3">
                        <input type="datetime-local" x-model="waktuDijadwalkan" :min="nowISO"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border p-6 sticky top-24">
                {{-- Poin Loyalty --}}
                <div x-show="poinBalance > 0" class="mb-6 p-4 bg-purple-50 border border-purple-200 rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-semibold text-purple-700">🎖️ Poin Loyalty</span>
                        <span class="text-purple-700 font-bold" x-text="poinBalance + ' poin'"></span>
                    </div>
                    <p class="text-xs text-purple-500 mb-2">100 poin = Rp 10.000 diskon. Masukkan jumlah poin yang ingin ditukar:</p>
                    <div class="flex gap-2">
                        <input type="number" x-model.number="poinInput" min="0" :max="poinBalance" step="100"
                            class="flex-1 border border-purple-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white"
                            placeholder="0">
                        <button @click="pakaiPoin()"
                            class="bg-purple-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-purple-700 transition disabled:opacity-50"
                            :disabled="!poinInput || poinInput < 100 || poinInput > poinBalance">
                            Pakai
                        </button>
                    </div>
                    <div x-show="poinDipakai > 0" class="mt-2 flex justify-between items-center">
                        <span class="text-sm text-purple-600" x-text="'Potongan: Rp ' + diskonPoin.toLocaleString('id-ID')"></span>
                        <button @click="batalkanPoin()" class="text-xs text-red-500 hover:text-red-700 font-medium">Batalkan</button>
                    </div>
                </div>

                <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                    <span>💳</span> Pembayaran
                </h3>

                <div class="space-y-3 mb-6">
                    <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition"
                        :class="metodePembayaran === 'midtrans' ? 'border-blue-600 bg-blue-50' : 'hover:bg-blue-50'">
                        <input type="radio" value="midtrans" x-model="metodePembayaran" class="accent-blue-600">
                        <div>
                            <span class="font-medium text-gray-800">Midtrans</span>
                            <p class="text-xs text-gray-400">QRIS / E-Wallet</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition"
                        :class="metodePembayaran === 'cash' ? 'border-blue-600 bg-blue-50' : 'hover:bg-blue-50'">
                        <input type="radio" value="cash" x-model="metodePembayaran" class="accent-blue-600">
                        <div>
                            <span class="font-medium text-gray-800">Bayar di Kasir</span>
                            <p class="text-xs text-gray-400">Cash / Tunai</p>
                        </div>
                    </label>
                </div>

                <div class="space-y-2 border-t border-gray-100 pt-4 mb-6">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                    </div>
                    <div x-show="voucherTerpakai" class="flex justify-between text-sm text-green-600">
                        <span>Diskon Voucher</span>
                        <span x-text="'-Rp ' + voucherTerpakai.diskon.toLocaleString('id-ID')"></span>
                    </div>
                    <div x-show="diskonPoin > 0" class="flex justify-between text-sm text-purple-600">
                        <span>Diskon Poin</span>
                        <span x-text="'-Rp ' + diskonPoin.toLocaleString('id-ID')"></span>
                    </div>
                    <div x-show="ongkir !== null && tipePengambilan === 'delivery'" class="flex justify-between text-sm text-blue-600">
                        <span>Ongkos Kirim</span>
                        <span x-text="ongkir > 0 ? 'Rp ' + ongkir.toLocaleString('id-ID') : 'Gratis'"></span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-gray-800 pt-2 border-t border-gray-100">
                        <span>Total Bayar</span>
                        <span class="text-blue-700" x-text="'Rp ' + totalBayar.toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <div x-show="submitting" class="text-center py-4">
                    <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                    <p class="text-sm text-gray-500">Memproses pesanan...</p>
                </div>

                <button @click="checkout()" x-show="!submitting"
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3.5 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition font-medium shadow-md active:scale-[0.98]">
                    <span x-text="tombolLabel"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
function checkoutApp() {
    return {
        cart: [],
        total: 0,
        outletDipilih: null,
        tipePengambilan: 'ditempat',
        noMeja: '',
        metodePembayaran: 'midtrans',
        loading: true,
        submitting: false,

        kodeVoucher: '',
        cekVoucherLoading: false,
        voucherTerpakai: null,
        voucherError: '',

        poinBalance: 0,
        poinInput: 0,
        poinDipakai: 0,
        diskonPoin: 0,

        alamatPengiriman: '',
        latPengiriman: null,
        lngPengiriman: null,
        ongkir: null,
        ongkirLoading: false,
        jadwalPengiriman: 'sekarang',
        waktuDijadwalkan: '',

        mapInitialized: false,
        map: null,
        marker: null,
        geoTimeout: null,

        get totalSetelahDiskon() {
            return this.total - (this.voucherTerpakai?.diskon || 0);
        },

        get totalBayar() {
            let t = this.totalSetelahDiskon - this.diskonPoin;
            if (this.tipePengambilan === 'delivery' && this.ongkir) {
                t += this.ongkir;
            }
            return Math.max(t, 0);
        },

        get bisaCheckout() {
            if (this.tipePengambilan === 'pickup') return true;
            if (this.tipePengambilan === 'ditempat') return !!this.noMeja;
            if (this.tipePengambilan === 'delivery') {
                return !!this.alamatPengiriman && !!this.latPengiriman && !!this.lngPengiriman;
            }
            return false;
        },

        get nowISO() {
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            return now.toISOString().slice(0, 16);
        },

        get tombolLabel() {
            if (this.tipePengambilan === 'delivery') return 'Pesan untuk Delivery';
            if (this.tipePengambilan === 'pickup') return 'Pesan untuk Pickup';
            return 'Buat Pesanan';
        },

        init() {
            this.outletDipilih = getOutlet();
            if (!this.outletDipilih) { this.loading = false; return; }

            const token = getToken();
            if (!token) { window.location.href = '{{ route("login") }}'; return; }
            axios.get('/api/cart', { headers: { Authorization: 'Bearer ' + token } })
                .then(res => {
                    this.cart = Object.values(res.data.cart);
                    this.total = res.data.total;
                    if (!this.cart.length) { window.location.href = '{{ route("menu") }}'; }
                })
                .catch(() => {
                    if (!getToken()) window.location.href = '{{ route("login") }}';
                })
                .finally(() => { this.loading = false; });

            axios.get('/api/loyalty', { headers: { Authorization: 'Bearer ' + token } })
                .then(res => {
                    this.poinBalance = res.data.balance;
                })
                .catch(() => {});

            this.$watch('tipePengambilan', (val) => {
                if (val === 'delivery') {
                    this.$nextTick(() => this.initMap());
                }
            });

            this.$watch('latPengiriman', () => {
                if (this.latPengiriman && this.lngPengiriman) {
                    this.hitungOngkir();
                }
            });
            this.$watch('lngPengiriman', () => {
                if (this.latPengiriman && this.lngPengiriman) {
                    this.hitungOngkir();
                }
            });

            this.$watch('alamatPengiriman', (val) => {
                if (this.geoTimeout) clearTimeout(this.geoTimeout);
                if (!val || val.length < 5) return;
                this.geoTimeout = setTimeout(() => this.cariLokasi(val), 800);
            });
        },

        initMap() {
            if (this.mapInitialized) return;
            const el = document.getElementById('map-picker');
            if (!el) return;

            const defaultLat = parseFloat(this.outletDipilih.latitude) - 0.01;
            const defaultLng = parseFloat(this.outletDipilih.longitude);

            this.map = L.map('map-picker', { zoomControl: true }).setView([defaultLat, defaultLng], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(this.map);

            this.map.on('click', (e) => {
                this.setMarker(e.latlng.lat, e.latlng.lng);
            });

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        this.setMarker(pos.coords.latitude, pos.coords.longitude);
                        this.map.setView([pos.coords.latitude, pos.coords.longitude], 15);
                    },
                    () => {},
                    { enableHighAccuracy: true, timeout: 5000 }
                );
            }

            setTimeout(() => this.map.invalidateSize(), 500);
            this.mapInitialized = true;
        },

        setMarker(lat, lng) {
            this.latPengiriman = lat;
            this.lngPengiriman = lng;
            if (this.marker) this.marker.setLatLng([lat, lng]);
            else this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);

            if (this.map) this.map.setView([lat, lng], 15);

            this.marker.on('dragend', () => {
                const pos = this.marker.getLatLng();
                this.latPengiriman = pos.lat;
                this.lngPengiriman = pos.lng;
            });
        },

        cariLokasi(alamat) {
            fetch('https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(alamat) + '&format=json&limit=1&accept-language=id')
            .then(r => r.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
                    if (this.mapInitialized) {
                        this.setMarker(lat, lng);
                    } else {
                        this.latPengiriman = lat;
                        this.lngPengiriman = lng;
                    }
                }
            })
            .catch(() => {});
        },

        hitungOngkir() {
            if (!this.latPengiriman || !this.lngPengiriman) return;
            this.ongkirLoading = true;
            const token = getToken();

            axios.post('/api/checkout/hitung-ongkir', {
                outlet_id: this.outletDipilih.id,
                latitude_pengiriman: this.latPengiriman,
                longitude_pengiriman: this.lngPengiriman,
                total_belanja: this.totalSetelahDiskon,
            }, { headers: { Authorization: 'Bearer ' + token } })
            .then(res => {
                this.ongkir = res.data.ongkir;
            })
            .catch(() => {
                this.ongkir = null;
            })
            .finally(() => {
                this.ongkirLoading = false;
            });
        },

        applyVoucher() {
            if (!this.kodeVoucher) return;
            this.cekVoucherLoading = true;
            this.voucherError = '';
            const token = getToken();

            axios.post('/api/checkout/cekVoucher', { kode: this.kodeVoucher, total: this.total }, {
                headers: { Authorization: 'Bearer ' + token }
            }).then(res => {
                this.voucherTerpakai = res.data;
                this.voucherError = '';
            }).catch(err => {
                this.voucherTerpakai = null;
                this.voucherError = err.response?.data?.message || 'Voucher tidak valid';
            }).finally(() => {
                this.cekVoucherLoading = false;
            });
        },

        hapusVoucher() {
            this.voucherTerpakai = null;
            this.kodeVoucher = '';
            this.voucherError = '';
        },

        pakaiPoin() {
            const pts = parseInt(this.poinInput);
            if (!pts || pts < 100 || pts > this.poinBalance) return;
            const discount = Math.floor(pts / 100) * 10000;
            this.poinDipakai = pts;
            this.diskonPoin = discount;
        },

        batalkanPoin() {
            this.poinDipakai = 0;
            this.diskonPoin = 0;
            this.poinInput = 0;
        },

        checkout() {
            console.log('checkout() called, mode:', this.tipePengambilan);
            if (!this.outletDipilih) {
                showToast('Pilih outlet terlebih dahulu', 'error');
                return;
            }
            if (this.tipePengambilan === 'ditempat' && !this.noMeja) {
                showToast('Silakan isi nomor meja', 'error');
                return;
            }
            if (this.tipePengambilan === 'delivery' && (!this.alamatPengiriman || !this.latPengiriman || !this.lngPengiriman)) {
                showToast('Lengkapi alamat & pilih lokasi di peta', 'error');
                return;
            }
            this.submitting = true;
            const token = getToken();

            const payload = {
                outlet_id: this.outletDipilih.id,
                tipe_pengambilan: this.tipePengambilan,
                metode_pembayaran: this.metodePembayaran,
            };

            if (this.tipePengambilan === 'ditempat') {
                payload.no_meja = this.noMeja;
            }

            if (this.tipePengambilan === 'delivery') {
                payload.alamat_pengiriman = this.alamatPengiriman;
                payload.latitude_pengiriman = this.latPengiriman;
                payload.longitude_pengiriman = this.lngPengiriman;
            }

            if (this.jadwalPengiriman === 'dijadwalkan' && this.waktuDijadwalkan) {
                payload.waktu_pengiriman_dijadwalkan = this.waktuDijadwalkan;
            }

            if (this.voucherTerpakai) {
                payload.kode_voucher = this.kodeVoucher;
            }

            if (this.poinDipakai > 0) {
                payload.poin_dipakai = this.poinDipakai;
            }

            axios.post('/api/checkout', payload, {
                headers: { Authorization: 'Bearer ' + token }
            }).then(res => {
                this.submitting = false;
                if (res.data.snap_token) {
                    if (typeof snap !== 'undefined') {
                        try {
                            snap.pay(res.data.snap_token, {
                                onSuccess: async () => {
                                    try {
                                        await fetch('/orders/confirm/' + res.data.transaksi.id, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Content-Type': 'application/json'
                                            }
                                        });
                                    } catch (e) { /* ignore */ }
                                    window.location.href = '{{ route("orders") }}';
                                },
                                onPending: () => { window.location.href = '{{ route("orders") }}'; },
                                onError: () => { window.location.href = '{{ route("orders") }}'; }
                            });
                        } catch (e) {
                            console.error('snap.pay error:', e);
                            showToast('Gagal membuka pembayaran: ' + e.message, 'error');
                            this.submitting = false;
                        }
                    } else {
                        showToast('Gagal memuat pembayaran. Muat ulang halaman.', 'error');
                    }
                } else {
                    showToast('Pesanan berhasil dibuat!', 'success');
                    setTimeout(() => { window.location.href = '{{ route("orders") }}'; }, 1000);
                }
            }).catch(err => {
                this.submitting = false;
                showToast(err.response?.data?.message || 'Gagal checkout', 'error');
            });
        }
    }
}
</script>
@endpush
