<?php

namespace App\Imports;

use App\Product;
use Maatwebsite\Excel\Concerns\ToModel;
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

        $product->save();

        return $product ;
    }
}
