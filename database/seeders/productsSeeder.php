<?php

namespace Database\Seeders;

use App\Models\products;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class productsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['code' => "3423423454", 'name' => "Battery CN45", "price" => 4575, 'catID' => 1, 'sale_percentage' => 23, 'extra_tax' => 0.1],
            ['code' => "3423423455", 'name' => "565 Watt N-Type Solar Panel", "price" => 24000, 'catID' => 1, 'sale_percentage' => 23, 'extra_tax' => 0.1],
            ['code' => "3423423456", 'name' => "Battery", "price" => 17000, 'catID' => 2, 'sale_percentage' => 23, 'extra_tax' => 0.1],
        ];
        products::insert($data);
    }
}
