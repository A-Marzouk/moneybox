<?php

namespace App\Http\Controllers;

use App\classes\Notification;
use App\Exports\ProductsExport;
use App\Product;
use danielme85\CConverter\Currency;
use Illuminate\Http\Request;


use App\classes\Upload;
use App\Imports\ProductsImport;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

class ProductsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return array of objects
     */
    public function getProducts()
    {
        $products = Product::orderBy('name','asc')->paginate(20);
        $limit = Input::get('limit');
        if(isset($limit)){
            $products = Product::orderBy('name','asc')->paginate(20);
        }

        return $products;
    }

    public function addProduct(Request $request){
        $this->validate($request, [
            'name' => 'required|max:191',
            'quantity' => 'max:191',
            'currency' => 'max:191',
            'buy_price' => 'required|max:191',
            'date' => 'max:191',
            'supplier' => 'max:191',
        ]);

        $product =  Product::create($request->all());

        $currency  = new Currency();

        if($product['currency'] !== 'UAH'){
            $product['buy_price_uah'] = $currency->convert($product['currency'], $to = 'UAH', $product['buy_price'] , $decimals = 2);
        }else{
            $product['buy_price_uah'] = $product['buy_price'] ;
        }

        $product->update([
            'buy_price_uah' =>  $product['buy_price_uah']
        ]);

        return $product ;

    }

    public function updateProduct(Request $request){
        $this->validate($request, [
            'name' => 'required|max:191',
            'quantity' => 'max:191',
            'currency' => 'max:191',
            'buy_price' => 'required|max:191',
        ]);

        $product = Product::find($request->id);
        $product->update(
            [
                'name' => $request->name,
                'quantity' => $request->quantity,
                'currency' => $request->currency,
                'buy_price' => $request->buy_price,
                'date' => $request->date,
                'supplier' => $request->supplier,
            ]
        );

        $currency  = new Currency();

        if($product['currency'] !== 'UAH'){
            $product['buy_price_uah'] = $currency->convert($product['currency'], $to = 'UAH', $product['buy_price'] , $decimals = 2);
        }else{
            $product['buy_price_uah'] = $product['buy_price'] ;
        }

        $product->update([
            'buy_price_uah' =>  $product['buy_price_uah']
        ]);


        return $product ;

    }

    public function deleteProduct(Request $request){
        return Product::destroy($request->product_id);
    }


    public function export()
    {
        Notification::productsAction('Exported');
        return Excel::download(new ProductsExport, 'products.xlsx');
    }
    public function import(Request $request)
    {

        $result  = Upload::productsSheet('productsExcelSheet',date(time()) );
        $filePth = $result['path'];

        Excel::import(new ProductsImport, $filePth);
        Notification::productsAction('Imported');
        return redirect(route('admin.dashboard'));
    }


    public function productsByCurrency($currency){
        // convert all prices to usd :
        $products = Product::all()->toArray();
        if($currency === 'UAH'){
            return $products ;
        }elseif ($currency === 'USD'){
            $to = 'USD';
        }elseif ($currency === 'EUR'){
            $to = 'EUR';
        }else{
            return;
        }

        $currency  = new Currency();
        foreach ($products as &$product) {
            $product['buy_price_new_currency'] = $currency->convert($from = 'UAH', $to, $product['buy_price'] , $decimals = 2);
        }

        return $products;
    }

    public function getCurrencyRate(){
        $currency  = new Currency();
        $usdRate = $currency->convert('USD', $to = 'UAH', 1 , $decimals = 4);
        $eurRate = $currency->convert('EUR', $to = 'UAH', 1 , $decimals = 4);

        return [
          'usd_rate' =>  $usdRate,
          'eur_rate' =>  $eurRate,
        ];
    }


}
