<?php

namespace App\Http\Controllers;

use App\classes\Notification;
use App\Client;
use App\Cost;
use App\Exports\SalesExport;
use danielme85\CConverter\Currency;
use App\Product;
use App\Sale;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getSalesList(){
        return Sale::with('product','client','costs')->where('client_id',currentClient()->id)->get();
    }

    public function addSale(Request $request){
        $this->validate($request, [
            'products_quantity' => 'required|max:191',
            'created_at' => 'required',
            'sell_price' => 'required|max:191',
            'product_id' => 'required|max:191',
            'client_id' =>  'required|max:191',
            'bonus' =>  'required|max:191',
            'for_new_client' =>  'max:191',
        ]);

        // sell price should be bigger than buy_price
        $product = Product::where('id',$request->product_id)->first();

        $currency  = new Currency();
        $usd_rate = $currency->convert('USD', $to = 'UAH', 1 , $decimals = 4);
        $eur_rate = $currency->convert('EUR', $to = 'UAH', 1 , $decimals = 4);

        $buy_price = $product->buy_price ;

        if($product->currency === 'USD'){
            $buy_price = $product->buy_price * $usd_rate  ;
        }

        if($product->currency === 'EUR'){
            $buy_price = $product->buy_price * $eur_rate  ;
        }


        if($request->sell_price < $buy_price){
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'ErrorInPrice' => ['Цена продажи не может быть меньше цены покупки..'],
            ]);
            throw $error;
        }


        if($request->products_quantity > $product->quantity){
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'ErrorInQuantity' => ['Недостаточно продуктов.'],
            ]);
            throw $error;
        }


        if(isset($request->costs)){
            if($request->bonus < 0){
                $error = \Illuminate\Validation\ValidationException::withMessages([
                    'ErrorInPrice' => ['Высокие расходы.'],
                ]);
                throw $error;
            }
        }




        $sale = Sale::create($request->all());
        // update products quantity :

        $product->update([
            'quantity' => $product->quantity - $sale->products_quantity
        ]);

        $sale['product'] = $sale->product;
        $sale['client']  = $sale->client;

        // create costs
        if(isset($request->costs)){
            foreach ($request->costs as $cost){
                if($cost['cost'] > 0){
                    Cost::create([
                        'label'   => $cost['label'],
                        'cost'    => $cost['cost'],
                        'sale_id' => $sale->id,
                    ]);
                }
            }
        }


        $sale['costs']   = $sale->costs;
        return $sale ;
    }


    public function editSale(Request $request){
        $this->validate($request, [
            'products_quantity' => 'required|max:191',
            'sell_price' => 'required|max:191',
            'product_id' => 'required|max:191',
            'client_id' =>  'required|max:191',
            'bonus' =>  'required|max:191',
            'for_new_client' =>  'max:191',
            'created_at' =>  'required',
        ]);

        // sell price should be bigger than buy_price
        $product = Product::where('id',$request->product_id)->first();
        $sale = Sale::where('id',$request->id)->first();

        $currency  = new Currency();
        $usd_rate = $currency->convert('USD', $to = 'UAH', 1 , $decimals = 4);
        $eur_rate = $currency->convert('EUR', $to = 'UAH', 1 , $decimals = 4);

        $buy_price = $product->buy_price ;

        if($product->currency === 'USD'){
            $buy_price = $product->buy_price * $usd_rate  ;
        }

        if($product->currency === 'EUR'){
            $buy_price = $product->buy_price * $eur_rate  ;
        }


        if($request->sell_price < $buy_price){
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'ErrorInPrice' => ['Цена продажи не может быть меньше цены покупки..'],
            ]);
            throw $error;
        }


        // old quantity of sale products
        // current quality of sale products
        // quantity of product it self

        $newSaleProductsQuantity     = $request->products_quantity ;
        $oldSaleProductsQuantity     = $sale->products_quantity ;
        $currentProductsQuantity     = $product->quantity;

        if($currentProductsQuantity - ($newSaleProductsQuantity - $oldSaleProductsQuantity) < 0){
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'ErrorInQuantity' => ['Недостаточно продуктов.'],
            ]);
            throw $error;
        }


        if(isset($request->costs)){
            if($request->bonus < 0){
                $error = \Illuminate\Validation\ValidationException::withMessages([
                    'ErrorInPrice' => ['Высокие расходы.'],
                ]);
                throw $error;
            }
        }



        $sale->update($request->all());
        // update products quantity :


        $product->update([
            'quantity' => $currentProductsQuantity - ($newSaleProductsQuantity - $oldSaleProductsQuantity)
        ]);

        $sale['product'] = $sale->product;
        $sale['client']  = $sale->client;

//        // create costs
//        if(isset($request->costs)){
//            foreach ($request->costs as $cost){
//                if($cost['cost'] > 0){
//                    Cost::create([
//                        'label'   => $cost['label'],
//                        'cost'    => $cost['cost'],
//                        'sale_id' => $sale->id,
//                    ]);
//                }
//            }
//        }
//
//
//        $sale['costs']   = $sale->costs;
        return [
            'status' => 'success',
            'sale' => $sale
        ] ;
    }

    public function deleteSale(Request $request){
        // check the quantity and put it back to the product :
        $sale = Sale::where('id',$request->sale_id)->first();
        $product = Product::where('id',$sale->product_id)->first();
        $product->update([
            'quantity' => $product->quantity + $sale->products_quantity
        ]);
        return Sale::destroy($request->sale_id);
    }

    public function export($client_id)
    {
        // check if client exists :
        $client = Client::where('id',$client_id)->first() ;
        if($client){
            Notification::productsAction('Client sales');
            return Excel::download(new SalesExport($client_id), $client->user->name .'.xlsx');
        }
    }
}
