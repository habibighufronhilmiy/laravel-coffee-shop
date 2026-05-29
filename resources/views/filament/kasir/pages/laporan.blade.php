<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Filter Tanggal
        </x-slot>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                {{ $this->form }}
            </div>
            <div class="flex items-end">
                <x-filament::button wire:click="loadData" color="primary" icon="heroicon-o-funnel">
                    Terapkan Filter
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">
            Ringkasan
        </x-slot>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <p class="text-sm text-gray-500">Total Pendapatan</p>
                <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <p class="text-sm text-gray-500">Total Transaksi</p>
                <p class="text-2xl font-bold text-blue-600">{{ $totalTransaksi }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <p class="text-sm text-gray-500">Menu Terjual</p>
                <p class="text-2xl font-bold text-blue-600">{{ $totalMenuTerjual }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <p class="text-sm text-gray-500">Total Pelanggan</p>
                <p class="text-2xl font-bold text-purple-600">{{ $totalPelanggan }}</p>
            </div>
        </div>
    </x-filament::section>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-filament::section>
            <x-slot name="heading">
                Menu Terlaris
            </x-slot>
            @forelse($menuTerlaris as $item)
                <div class="flex justify-between py-2 border-b last:border-0">
                    <span>{{ $item['menu']['nama_menu'] ?? '-' }}</span>
                    <span class="font-medium">{{ $item['total_terjual'] }} terjual</span>
                </div>
            @empty
                <p class="text-gray-400 text-sm">Belum ada data</p>
            @endforelse
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Pendapatan Bulanan
            </x-slot>
            @forelse($transaksiBulanan as $item)
                <div class="flex justify-between py-2 border-b last:border-0">
                    <span>{{ \Carbon\Carbon::createFromFormat('Y-m', $item['bulan'])->locale('id')->translatedFormat('F Y') }}</span>
                    <span class="font-medium">Rp {{ number_format($item['total'], 0, ',', '.') }}</span>
                </div>
            @empty
                <p class="text-gray-400 text-sm">Belum ada data</p>
            @endforelse
        </x-filament::section>
    </div>
</x-filament-panels::page>
