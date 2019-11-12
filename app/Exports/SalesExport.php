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
        $sales = Sale::select('product_id','products_quantity','sell_price','bonus')->where('client_id',$this->client_id)->get();
        foreach ($sales as &$sale){
            $product = Product::where('id', $sale['product_id'])->first() ;
            if(!isset($product->name)){
                continue ;
            }
            $sale['product_id'] = $product->name ;
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
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
