<?php

namespace App\Imports;

use App\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use danielme85\CConverter\Currency;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        $product = Product::find($row['id']);

        if(!$product){
            if(empty($row['item'])){
                return;
            }
           // create new product with the data
            $product = new Product ;
        }

        $product->name = $row['item'];
        $product->quantity = $row['quantity'];
        $product->currency = $row['currency'];
        $product->date = $row['date'];
        $product->supplier = $row['supplier'];
        $product->buy_price = $row['price'];
        $product->buy_price_uah = $row['price_uah'];

        $currency  = new Currency();

        if($product->currency !== 'UAH' && $product->buy_price_uah == null){
            $product->buy_price_uah  = $currency->convert($product->currency, $to = 'UAH', $product->buy_price , $decimals = 2);
        }else{
            $product->buy_price_uah = $product->buy_price ;
        }

        $product->save();

        return $product ;
    }
}
