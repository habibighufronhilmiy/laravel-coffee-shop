<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Menu;
use App\Models\MenuOptionGroup;
use App\Models\MenuOptionGroupItem;
use App\Models\MenuVariant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DemoSeeder::class,
        ]);

        User::create([
            'name' => 'Admin Tens Coffee',
            'email' => 'admin@tenscoffee.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Kasir Tens Coffee',
            'email' => 'kasir@tenscoffee.com',
            'password' => bcrypt('password'),
            'role' => 'kasir',
        ]);

        User::create([
            'name' => 'Customer Demo',
            'email' => 'customer@tenscoffee.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $kopi = Kategori::create(['nama_kategori' => 'Kopi', 'icon' => 'coffee']);
        $nonKopi = Kategori::create(['nama_kategori' => 'Non Kopi', 'icon' => 'beverage']);
        $makanan = Kategori::create(['nama_kategori' => 'Makanan', 'icon' => 'bakery']);

        $espresso = Menu::create([
            'kategori_id' => $kopi->id, 'nama_menu' => 'Espresso',
            'deskripsi' => 'Kopi hitam pekat yang dibuat dengan menekan air panas melalui bubuk kopi halus.',
            'harga' => 25000, 'stok' => 50,
        ]);
        MenuVariant::create(['menu_id' => $espresso->id, 'nama' => 'Hot', 'harga_tambahan' => 0, 'stok' => null]);
        MenuVariant::create(['menu_id' => $espresso->id, 'nama' => 'Cold', 'harga_tambahan' => 0, 'stok' => null]);

        $this->seedOptionGroups($espresso);

        $cappuccino = Menu::create([
            'kategori_id' => $kopi->id, 'nama_menu' => 'Cappuccino',
            'deskripsi' => 'Kopi dengan campuran espresso, susu panas, dan busa susu yang lembut.',
            'harga' => 35000, 'stok' => 40,
        ]);
        MenuVariant::create(['menu_id' => $cappuccino->id, 'nama' => 'Hot', 'harga_tambahan' => 0, 'stok' => null]);
        MenuVariant::create(['menu_id' => $cappuccino->id, 'nama' => 'Cold', 'harga_tambahan' => 0, 'stok' => null]);

        $this->seedOptionGroups($cappuccino);

        $latte = Menu::create([
            'kategori_id' => $kopi->id, 'nama_menu' => 'Latte',
            'deskripsi' => 'Espresso dengan susu steamed yang creamy dan sedikit busa di atasnya.',
            'harga' => 35000, 'stok' => 40,
        ]);
        MenuVariant::create(['menu_id' => $latte->id, 'nama' => 'Hot', 'harga_tambahan' => 0, 'stok' => null]);
        MenuVariant::create(['menu_id' => $latte->id, 'nama' => 'Cold', 'harga_tambahan' => 0, 'stok' => null]);

        $this->seedOptionGroups($latte);

        $mocha = Menu::create([
            'kategori_id' => $kopi->id, 'nama_menu' => 'Mocha',
            'deskripsi' => 'Perpaduan espresso dengan coklat dan susu steamed, topping whipped cream.',
            'harga' => 40000, 'stok' => 30,
        ]);
        MenuVariant::create(['menu_id' => $mocha->id, 'nama' => 'Hot', 'harga_tambahan' => 0, 'stok' => null]);
        MenuVariant::create(['menu_id' => $mocha->id, 'nama' => 'Cold', 'harga_tambahan' => 0, 'stok' => null]);

        $this->seedOptionGroups($mocha);

        Menu::create([
            'kategori_id' => $nonKopi->id, 'nama_menu' => 'Matcha Latte',
            'deskripsi' => 'Minuman berbasis matcha premium dengan susu segar, rasa autentik Jepang.',
            'harga' => 35000, 'stok' => 35,
        ]);
        Menu::create([
            'kategori_id' => $nonKopi->id, 'nama_menu' => 'Chocolate',
            'deskripsi' => 'Coklat panas creamy dengan cita rasa rich dan manis pas.',
            'harga' => 30000, 'stok' => 45,
        ]);
        Menu::create([
            'kategori_id' => $nonKopi->id, 'nama_menu' => 'Lemon Tea',
            'deskripsi' => 'Teh segar dengan perasan lemon asli, menyegarkan dahaga.',
            'harga' => 20000, 'stok' => 60,
        ]);
        Menu::create([
            'kategori_id' => $makanan->id, 'nama_menu' => 'Croissant',
            'deskripsi' => 'Croissant panggang renyah dengan butter Prancis asli.',
            'harga' => 28000, 'stok' => 20,
        ]);
        Menu::create([
            'kategori_id' => $makanan->id, 'nama_menu' => 'Cheesecake',
            'deskripsi' => 'New York style cheesecake with berry topping, creamy dan lembut.',
            'harga' => 35000, 'stok' => 15,
        ]);
        Menu::create([
            'kategori_id' => $makanan->id, 'nama_menu' => 'Nasi Goreng',
            'deskripsi' => 'Nasi goreng spesial dengan telur, ayam suwir, dan kerupuk.',
            'harga' => 45000, 'stok' => 25,
        ]);
    }

    private function seedOptionGroups($menu): void
    {
        $gula = MenuOptionGroup::create(['menu_id' => $menu->id, 'nama' => 'Sugar Level', 'tipe' => 'single', 'urutan' => 1]);
        MenuOptionGroupItem::create(['menu_option_group_id' => $gula->id, 'nama' => 'Less Sugar', 'harga_tambahan' => 0, 'urutan' => 1]);
        MenuOptionGroupItem::create(['menu_option_group_id' => $gula->id, 'nama' => 'Normal Sugar', 'harga_tambahan' => 0, 'urutan' => 2, 'is_default' => true]);

        $size = MenuOptionGroup::create(['menu_id' => $menu->id, 'nama' => 'Size', 'tipe' => 'single', 'urutan' => 2]);
        MenuOptionGroupItem::create(['menu_option_group_id' => $size->id, 'nama' => 'Regular', 'harga_tambahan' => 0, 'urutan' => 1, 'is_default' => true]);
        MenuOptionGroupItem::create(['menu_option_group_id' => $size->id, 'nama' => 'Large', 'harga_tambahan' => 5000, 'urutan' => 2]);
        MenuOptionGroupItem::create(['menu_option_group_id' => $size->id, 'nama' => 'Jumbo', 'harga_tambahan' => 8000, 'urutan' => 3]);

        $es = MenuOptionGroup::create(['menu_id' => $menu->id, 'nama' => 'Ice Level', 'tipe' => 'single', 'urutan' => 3]);
        MenuOptionGroupItem::create(['menu_option_group_id' => $es->id, 'nama' => 'Normal Ice', 'harga_tambahan' => 0, 'urutan' => 1, 'is_default' => true]);
        MenuOptionGroupItem::create(['menu_option_group_id' => $es->id, 'nama' => 'Less Ice', 'harga_tambahan' => 0, 'urutan' => 2]);
        MenuOptionGroupItem::create(['menu_option_group_id' => $es->id, 'nama' => 'No Ice', 'harga_tambahan' => 0, 'urutan' => 3]);

        $addon = MenuOptionGroup::create(['menu_id' => $menu->id, 'nama' => 'Add-ons', 'tipe' => 'multiple', 'urutan' => 4]);
        MenuOptionGroupItem::create(['menu_option_group_id' => $addon->id, 'nama' => 'Extra Shot', 'harga_tambahan' => 5000, 'urutan' => 1]);
        MenuOptionGroupItem::create(['menu_option_group_id' => $addon->id, 'nama' => 'Boba', 'harga_tambahan' => 3000, 'urutan' => 2]);
        MenuOptionGroupItem::create(['menu_option_group_id' => $addon->id, 'nama' => 'Whipped Cream', 'harga_tambahan' => 3000, 'urutan' => 3]);
        MenuOptionGroupItem::create(['menu_option_group_id' => $addon->id, 'nama' => 'Soy Milk', 'harga_tambahan' => 4000, 'urutan' => 4]);
    }
}
