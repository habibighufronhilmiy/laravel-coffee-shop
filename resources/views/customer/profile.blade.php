@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div x-data="profileApp()" x-cloak>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Profil Saya</h1>
        <p class="text-gray-500 mt-1">Kelola informasi akun kamu</p>
    </div>

    <div class="grid lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-2xl shadow-sm border p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Informasi Profil</h2>
            <form @submit.prevent="simpanProfil" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Nama</label>
                    <input type="text" x-model="form.name" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Email</label>
                    <input type="email" x-model="form.email" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">No. Telepon</label>
                    <input type="text" x-model="form.no_telp" placeholder="08xxxx"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                </div>
                <button type="submit" :disabled="saving"
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition font-medium shadow-md disabled:opacity-50">
                    <span x-show="!saving">Simpan Profil</span>
                    <span x-show="saving">Menyimpan...</span>
                </button>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Ganti Password</h2>
            <form @submit.prevent="gantiPassword" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Password Saat Ini</label>
                    <input type="password" x-model="passwordForm.current_password" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Password Baru</label>
                    <input type="password" x-model="passwordForm.password" required minlength="6"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" x-model="passwordForm.password_confirmation" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                </div>
                <button type="submit" :disabled="savingPassword"
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition font-medium shadow-md disabled:opacity-50">
                    <span x-show="!savingPassword">Ganti Password</span>
                    <span x-show="savingPassword">Menyimpan...</span>
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800">Alamat Saya</h2>
                <button @click="showAddressForm = true" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm hover:bg-blue-700 transition font-medium">
                    + Tambah Alamat
                </button>
            </div>

            <template x-if="addresses.length === 0">
                <p class="text-gray-400 text-center py-8">Belum ada alamat tersimpan</p>
            </template>

            <div class="grid sm:grid-cols-2 gap-4">
                <template x-for="addr in addresses" :key="addr.id">
                    <div class="border rounded-xl p-4 relative">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <span class="font-semibold text-gray-800" x-text="addr.label || 'Alamat'"></span>
                                <span x-show="addr.is_default" class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-medium">Utama</span>
                            </div>
                            <div class="flex gap-1">
                                <button @click="editAddress(addr)" class="text-xs text-blue-600 hover:underline">Edit</button>
                                <button @click="hapusAlamat(addr.id)" class="text-xs text-red-600 hover:underline">Hapus</button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600" x-text="addr.alamat"></p>
                        <div class="mt-1 text-xs text-gray-400">
                            <span x-show="addr.penerima" x-text="'Penerima: ' + addr.penerima"></span>
                            <span x-show="addr.no_telp_penerima" x-text="' | Telp: ' + addr.no_telp_penerima"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Address Form Modal --}}
    <div x-show="showAddressForm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
         @click.self="showAddressForm = false">
        <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold text-gray-800 mb-6" x-text="editingAddress ? 'Edit Alamat' : 'Tambah Alamat'"></h3>
            <form @submit.prevent="simpanAlamat" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Label</label>
                    <input type="text" x-model="addressForm.label" placeholder="Rumah, Kantor, dll"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Alamat</label>
                    <textarea x-model="addressForm.alamat" required rows="3"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">Penerima</label>
                        <input type="text" x-model="addressForm.penerima"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">No. Telp Penerima</label>
                        <input type="text" x-model="addressForm.no_telp_penerima"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="addressForm.is_default" class="rounded border-gray-300 text-blue-600">
                    <span class="text-sm text-gray-600">Jadikan alamat utama</span>
                </label>
                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="savingAddress"
                        class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition font-medium disabled:opacity-50">
                        <span x-show="!savingAddress" x-text="editingAddress ? 'Simpan' : 'Tambah'"></span>
                        <span x-show="savingAddress">Menyimpan...</span>
                    </button>
                    <button type="button" @click="showAddressForm = false"
                        class="px-6 py-3 border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function profileApp() {
    return {
        form: { name: '', email: '', no_telp: '' },
        passwordForm: { current_password: '', password: '', password_confirmation: '' },
        addresses: [],
        showAddressForm: false,
        editingAddress: null,
        addressForm: { label: '', alamat: '', penerima: '', no_telp_penerima: '', is_default: false },
        saving: false,
        savingPassword: false,
        savingAddress: false,

        init() {
            const token = getToken();
            if (!token) { window.location.href = '{{ route("login") }}'; return; }
            this.loadProfile();
            this.loadAddresses();
        },

        loadProfile() {
            const token = getToken();
            axios.get('/api/profile', { headers: { Authorization: 'Bearer ' + token } })
                .then(res => {
                    this.form.name = res.data.name;
                    this.form.email = res.data.email;
                    this.form.no_telp = res.data.no_telp || '';
                });
        },

        loadAddresses() {
            const token = getToken();
            axios.get('/api/addresses', { headers: { Authorization: 'Bearer ' + token } })
                .then(res => this.addresses = res.data);
        },

        simpanProfil() {
            this.saving = true;
            const token = getToken();
            axios.put('/api/profile', this.form, { headers: { Authorization: 'Bearer ' + token } })
                .then(res => {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Profil berhasil diperbarui', type: 'success' } }));
                })
                .catch(err => {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: err.response?.data?.message || 'Gagal menyimpan profil', type: 'error' } }));
                })
                .finally(() => { this.saving = false; });
        },

        gantiPassword() {
            this.savingPassword = true;
            const token = getToken();
            axios.put('/api/profile/password', this.passwordForm, { headers: { Authorization: 'Bearer ' + token } })
                .then(res => {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Password berhasil diubah', type: 'success' } }));
                    this.passwordForm = { current_password: '', password: '', password_confirmation: '' };
                })
                .catch(err => {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: err.response?.data?.message || 'Gagal mengubah password', type: 'error' } }));
                })
                .finally(() => { this.savingPassword = false; });
        },

        simpanAlamat() {
            this.savingAddress = true;
            const token = getToken();
            const url = this.editingAddress ? '/api/addresses/' + this.editingAddress.id : '/api/addresses';
            const method = this.editingAddress ? 'put' : 'post';
            axios[method](url, this.addressForm, { headers: { Authorization: 'Bearer ' + token } })
                .then(res => {
                    this.showAddressForm = false;
                    this.editingAddress = null;
                    this.addressForm = { label: '', alamat: '', penerima: '', no_telp_penerima: '', is_default: false };
                    this.loadAddresses();
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Alamat berhasil disimpan', type: 'success' } }));
                })
                .catch(err => {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: err.response?.data?.message || 'Gagal menyimpan alamat', type: 'error' } }));
                })
                .finally(() => { this.savingAddress = false; });
        },

        editAddress(addr) {
            this.editingAddress = addr;
            this.addressForm = {
                label: addr.label || '',
                alamat: addr.alamat,
                penerima: addr.penerima || '',
                no_telp_penerima: addr.no_telp_penerima || '',
                is_default: addr.is_default,
            };
            this.showAddressForm = true;
        },

        hapusAlamat(id) {
            if (!confirm('Hapus alamat ini?')) return;
            const token = getToken();
            axios.delete('/api/addresses/' + id, { headers: { Authorization: 'Bearer ' + token } })
                .then(res => {
                    this.loadAddresses();
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Alamat dihapus', type: 'success' } }));
                });
        }
    }
}
</script>
@endpush
