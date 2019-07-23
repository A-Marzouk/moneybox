<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

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
        return Product::all();
    }

    public function addProduct(Request $request){
        $this->validate($request, [
            'name' => 'required|max:191',
            'buy_price' => 'required|max:191',
        ]);

        return Product::create($request->all());
    }

    public function updateProduct(Request $request){
        $this->validate($request, [
            'name' => 'required|max:191',
            'buy_price' => 'required|max:191',
        ]);

        $product = Product::find($request->id);
        $product->update(
            [
                'name' => $request->name,
                'buy_price' => $request->buy_price
            ]
        );

        return $product ;

    }

    public function deleteProduct(Request $request){
        return Product::destroy($request->product_id);
    }


}
