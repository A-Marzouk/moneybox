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
use Spatie\Permission\Traits\HasRoles;

class Client extends Model
{
    use HasRoles;

    protected $table = 'clients';
    protected $fillable = [
        'name',
        'percentage',
        'plan'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sales(){
        return $this->hasMany(Sale::class);
    }

}
