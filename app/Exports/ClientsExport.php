<?php

namespace App\Exports;

use App\Client;
use App\Product;
use App\Sale;
use App\User;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ClientsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // this should return array of objects :
        $clients = Client::all('id','plan','percentage');
        foreach ($clients as &$client){
            $client['name']  = $client->user->name ;
            $client['email'] = $client->user->email ;
            $client['sales'] = URL::to('/'). '/client/export/sales/'.$client->id;
            $client['sales_link'] = URL::to('/'). '/client/export/sales/'.$client->id;
        }

        return $clients ;
    }

    public function headings(): array
    {
        return [
            'id',
            'plan',
            'percentage',
            'name',
            'email',
            'sales',
            'sales_link',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
