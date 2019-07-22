<?php
/**
 * Created by PhpStorm.
 * User: Ahmed
 * Date: 7/25/2018
 * Time: 7:24 PM
 */

namespace App;

use App\Models\Concerns\HasUser;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{

    protected $table = 'sales';


    public function client(){
        return $this->belongsTo(Client::class);
    }


    public function Product(){
        return $this->belongsTo(Product::class); // sales.product_id
    }

}
