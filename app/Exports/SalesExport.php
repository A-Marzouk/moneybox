<?php

namespace App\Exports;

use App\Client;
use App\Product;
use App\Sale;
use danielme85\CConverter\Currency;
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

            $sale['product_id'] = $product->name ;
            $sale['id'] = $product->supplier ;
            $sale['buy_price'] = $buy_price ;
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
