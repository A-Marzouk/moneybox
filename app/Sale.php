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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'products_quantity',
        'sell_price',
        'product_id',
        'client_id',
    ];

    /**
     * Add client ID be default to created sales.
     *
     * @return \App\User
     */
    public function setClientID()
    {
        $this->attributes['client_id'] = currentClient()->id;
        return $this;
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }


    public function Product(){
        return $this->belongsTo(Product::class); // sales.product_id
    }


    public function costs(){
        return $this->hasMany(Cost::class);
    }

}
