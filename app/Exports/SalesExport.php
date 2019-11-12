<?php

namespace App\Exports;

use App\Client;
use App\Product;
use App\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SalesExport implements FromCollection, WithHeadings
{
     public  $client_id ;

    public function __construct($client_id)
    {
        $this->client_id = $client_id ;
    }

    public function collection()
    {
        $sales = Sale::select('product_id','products_quantity','sell_price','bonus','id')->with('costs')->where('client_id',$this->client_id)->get();
        foreach ($sales as &$sale){
            $product = Product::where('id', $sale['product_id'])->first() ;
            if(!isset($product->name)){
                continue ;
            }

            $totalCosts = 0 ;
            foreach ($sale->costs as $cost){
                $totalCosts += $cost->cost ;
            }

            $sale['product_id'] = $product->name ;
            $sale['id'] = $product->supplier ;
            $sale['buy_price'] = $product->buy_price . ' | ' . $product->currency ;
            $sale['total_costs'] = $totalCosts ;
        }
        return $sales ;
    }

    public function headings(): array
    {
        return [
            'Продукт',
            'Количество',
            'Цена продажи',
            'Бонус',
            'Производтель',
            'себестоимость',
            'Доп. расходы',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
