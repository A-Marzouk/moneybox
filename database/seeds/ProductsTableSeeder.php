<?php

use App\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect([
            [
                'name' => 'Product one',
                'buy_price' => 500,
                'date' => '01.01.2019',
                'supplier' => 'Borsch',
            ],
            [
                'name' => 'Product two',
                'buy_price' => 1000,
                'date' => '01.01.2019',
                'supplier' => 'Borsch',
            ],
        ])->each(function ($item) {
            Product::create($item);
        });

    }
}
