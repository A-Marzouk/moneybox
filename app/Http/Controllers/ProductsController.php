<?php

namespace App\Http\Controllers;

use App\classes\Notification;
use App\Exports\ProductsExport;
use App\Product;
use Illuminate\Http\Request;


use App\classes\Upload;
use App\Imports\ProductsImport;
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
        return Product::all();
    }

    public function addProduct(Request $request){
        $this->validate($request, [
            'name' => 'required|max:191',
            'quantity' => 'max:191',
            'buy_price' => 'required|max:191',
            'date' => 'max:191',
            'supplier' => 'max:191',
        ]);

        return Product::create($request->all());
    }

    public function updateProduct(Request $request){
        $this->validate($request, [
            'name' => 'required|max:191',
            'quantity' => 'max:191',
            'buy_price' => 'required|max:191',
        ]);

        $product = Product::find($request->id);
        $product->update(
            [
                'name' => $request->name,
                'quantity' => $request->quantity,
                'buy_price' => $request->buy_price,
                'date' => $request->date,
                'supplier' => $request->supplier,
            ]
        );

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



}
