<?php
/**
 * Created by PhpStorm.
 * User: Ahmed-pc
 * Date: 8/27/2019
 * Time: 12:43 AM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{

    protected $table = 'costs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'label',
        'cost',
        'sale_id',
    ];


    public function sale(){
        return $this->belongsTo(Sale::class);
    }


}