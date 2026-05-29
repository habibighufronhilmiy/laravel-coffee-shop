<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\CartItem;
use App\Models\Kategori;
use App\Models\Menu;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Menu $menu;
    private Voucher $voucher;
    private Outlet $outlet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'customer']);
        $kategori = Kategori::create(['nama_kategori' => 'Kopi']);
        $this->menu = Menu::create([
            'kategori_id' => $kategori->id,
            'nama_menu' => 'Cappuccino',
            'deskripsi' => 'Enak',
            'harga' => 35000,
            'stok' => 10,
        ]);
        $this->voucher = Voucher::create([
            'kode' => 'TEST10',
            'nama' => 'Test Voucher',
            'tipe' => 'persen',
            'nilai' => 10,
            'min_belanja' => 30000,
            'maks_diskon' => 5000,
            'kuota' => 10,
            'terpakai' => 0,
            'berlaku_mulai' => now()->subDay(),
            'berlaku_sampai' => now()->addMonth(),
            'aktif' => true,
        ]);
        $this->outlet = Outlet::create([
            'nama' => 'Tens Coffee Pusat',
            'alamat' => 'Jl. Kopi Nikmat No.1',
            'latitude' => -6.2146,
            'longitude' => 106.8451,
            'no_telp' => '08123456789',
            'jam_buka' => '08:00',
            'jam_tutup' => '22:00',
            'aktif' => true,
        ]);
    }

    public function test_banners_api(): void
    {
        Banner::create([
            'judul' => 'Promo',
            'deskripsi' => 'Diskon',
            'gambar' => 'banners/test.png',
            'aktif' => true,
            'urutan' => 1,
        ]);

        $response = $this->getJson('/api/banners');
        $response->assertOk();
        $response->assertJsonCount(1);
    }

    public function test_menu_api(): void
    {
        $response = $this->getJson('/api/menu');
        $response->assertOk();
        $response->assertJsonCount(1);
    }

    public function test_cart_operations(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/cart/add', [
            'menu_id' => $this->menu->id,
            'jumlah' => 2,
        ]);
        $response->assertOk();
        $this->assertEquals('Cappuccino ditambahkan ke keranjang', $response->json('message'));

        $response = $this->getJson('/api/cart');
        $response->assertOk();
        $cart = $response->json('cart');
        $this->assertCount(1, $cart);
        $this->assertEquals(2, $cart[0]['jumlah']);
        $this->assertEquals(70000, $response->json('total'));

        $response = $this->putJson('/api/cart/update', [
            'id' => $cart[0]['id'],
            'menu_id' => $this->menu->id,
            'jumlah' => 3,
        ]);
        $response->assertOk();
        $this->assertEquals('Item diperbarui', $response->json('message'));

        $cartItemId = $response->json('cart')[0]['id'] ?? null;
        if ($cartItemId) {
            $response = $this->deleteJson('/api/cart/remove/' . $cartItemId);
        } else {
            $response = $this->deleteJson('/api/cart/remove/0');
        }
        $response->assertOk();
        $this->assertEquals('Item dihapus dari keranjang', $response->json('message'));

        $response = $this->getJson('/api/cart');
        $response->assertOk();
        $this->assertEmpty($response->json('cart'));
    }

    public function test_check_voucher(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/checkout/cekVoucher', [
            'kode' => 'TEST10',
            'total' => 70000,
        ]);
        $response->assertOk();
        $response->assertJson([
            'valid' => true,
            'diskon' => 5000,
            'total_setelah_diskon' => 65000,
        ]);
    }

    public function test_checkout_with_cash(): void
    {
        $this->actingAs($this->user);

        CartItem::create([
            'user_id' => $this->user->id,
            'menu_id' => $this->menu->id,
            'jumlah' => 2,
            'harga' => 35000,
            'subtotal' => 70000,
        ]);

        $response = $this->postJson('/api/checkout', [
            'outlet_id' => $this->outlet->id,
            'tipe_pengambilan' => 'ditempat',
            'no_meja' => '5',
            'metode_pembayaran' => 'cash',
        ]);
        $response->assertCreated();
        $response->assertJson([
            'message' => 'Pesanan berhasil dibuat. Silakan bayar di kasir.',
        ]);

        $this->assertDatabaseHas('transaksis', [
            'user_id' => $this->user->id,
            'total_harga' => 70000,
            'status_pembayaran' => 'lunas',
        ]);

        $this->assertDatabaseHas('menus', [
            'id' => $this->menu->id,
            'stok' => 8,
        ]);
    }

    public function test_checkout_with_voucher(): void
    {
        $this->actingAs($this->user);

        CartItem::create([
            'user_id' => $this->user->id,
            'menu_id' => $this->menu->id,
            'jumlah' => 3,
            'harga' => 35000,
            'subtotal' => 105000,
        ]);

        $response = $this->postJson('/api/checkout', [
            'outlet_id' => $this->outlet->id,
            'tipe_pengambilan' => 'ditempat',
            'no_meja' => '10',
            'metode_pembayaran' => 'cash',
            'kode_voucher' => 'TEST10',
        ]);
        $response->assertCreated();

        // 10% of 105000 = 10500, capped at maks_diskon 5000
        $this->assertDatabaseHas('transaksis', [
            'user_id' => $this->user->id,
            'total_harga' => 100000,
        ]);

        $this->assertDatabaseHas('voucher_pakai', [
            'diskon' => 5000,
        ]);

        $this->assertEquals(1, $this->voucher->fresh()->terpakai);
    }

    public function test_checkout_fails_on_insufficient_stock(): void
    {
        $this->actingAs($this->user);

        CartItem::create([
            'user_id' => $this->user->id,
            'menu_id' => $this->menu->id,
            'jumlah' => 999,
            'harga' => 35000,
            'subtotal' => 999 * 35000,
        ]);

        $response = $this->postJson('/api/checkout', [
            'outlet_id' => $this->outlet->id,
            'tipe_pengambilan' => 'ditempat',
            'no_meja' => '5',
            'metode_pembayaran' => 'cash',
        ]);
        $response->assertStatus(400);
        $response->assertJson(['message' => 'Stok tidak mencukupi.']);
    }

    public function test_orders_list_and_detail(): void
    {
        $this->actingAs($this->user);

        CartItem::create([
            'user_id' => $this->user->id,
            'menu_id' => $this->menu->id,
            'jumlah' => 1,
            'harga' => 35000,
            'subtotal' => 35000,
        ]);

        $this->postJson('/api/checkout', [
            'outlet_id' => $this->outlet->id,
            'tipe_pengambilan' => 'ditempat',
            'no_meja' => '3',
            'metode_pembayaran' => 'cash',
        ]);

        $response = $this->getJson('/api/orders');
        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_invalid_voucher_rejected(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/checkout/cekVoucher', [
            'kode' => 'INVALID',
            'total' => 50000,
        ]);
        $response->assertStatus(400);
    }
}
