<?php

namespace App\Exports;

use App\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProductsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Product::all(
            'supplier',
            'date',
            'name',
            'quantity',
            'currency',
            'buy_price',
            'buy_price_uah',
            'id'
        );
    }

    public function headings(): array
    {
        return [
            'supplier',
            'date',
            'item',
            'quantity',
            'currency',
            'price',
            'price_uah',
            'id'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
