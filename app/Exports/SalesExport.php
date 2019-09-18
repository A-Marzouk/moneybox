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
        $client = Client::where('id',$this->client_id)->first();
        return $client->sales ;
    }

    public function headings(): array
    {
        return [
            'id',
            'products_quantity',
            'sell_price',
            'product_id',
            'client_id',
            'bonus',
            'created_at',
            'updated_at',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}