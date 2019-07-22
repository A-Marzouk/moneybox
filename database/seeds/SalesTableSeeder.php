<?php

use App\Product;
use App\Sale;
use Illuminate\Database\Seeder;

class SalesTableSeeder extends Seeder
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
                'products_quantity' => 10,
                'sell_price' => 700,
                'product_id' => 1,
                'client_id' => 1,
            ],
            [
                'products_quantity' => 20,
                'sell_price' => 1200,
                'product_id' => 2,
                'client_id' => 1,
            ],
        ])->each(function ($item) {
            Sale::create($item);
        });

    }
}
