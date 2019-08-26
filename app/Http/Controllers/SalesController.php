<?php

namespace App\Http\Controllers;

use App\Product;
use App\Sale;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getSalesList(){
        return Sale::with('product','client')->where('client_id',currentClient()->id)->get();
    }

    public function addSale(Request $request){
        $this->validate($request, [
            'products_quantity' => 'required|max:191',
            'sell_price' => 'required|max:191',
            'product_id' => 'required|max:191',
            'client_id' =>  'required|max:191',
        ]);

        // sell price should be bigger than buy_price
        $product = Product::find($request->product_id)->first();
        if($request->sell_price < $product->buy_price){
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'ErrorInPrice' => ['Sell price is higher than buy price.'],
            ]);
            throw $error;
        }


        $sale = Sale::create($request->all());
        $sale['product'] = $sale->product;
        $sale['client']  = $sale->client;

        return $sale ;
    }

    public function deleteSale(Request $request){
        return Sale::destroy($request->sale_id);
    }
}
