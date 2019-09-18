<?php

namespace App\Http\Controllers;

use App\classes\Notification;
use App\Client;
use App\Cost;
use App\Exports\SalesExport;
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
            'sell_price' => 'required|max:191',
            'product_id' => 'required|max:191',
            'client_id' =>  'required|max:191',
            'bonus' =>  'required|max:191',
        ]);

        // sell price should be bigger than buy_price
        $product = Product::where('id',$request->product_id)->first();

        if($request->sell_price < $product->buy_price){
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'ErrorInPrice' => ['Sell price can not be less than buy price.'],
            ]);
            throw $error;
        }


        if($request->products_quantity > $product->quantity){
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'ErrorInQuantity' => ['Not enough products.'],
            ]);
            throw $error;
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

    public function deleteSale(Request $request){
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
