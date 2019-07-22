<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use ReflectionClass;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Hash any password being inserted by default.
     *
     * @param  string  $password
     *
     * @return \App\User
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);

        return $this;
    }

    public function createClient(array $attributes = [])
    {
        return $this->createRelated(Client::class, $attributes);
    }

    /**
     * Create a new related.
     *
     * @param  string  $class
     * @param  array  $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createRelated($class, array $attributes = [])
    {
        DB::beginTransaction();

        try {
            $classArrayKey = camel_case((new ReflectionClass($class))->getShortName());
            $modelData = array_key_exists($classArrayKey, $attributes) ? $attributes[$classArrayKey] : [];

            $user = static::fill($attributes['user']);
            $user->save();
            $user->assignRole($classArrayKey)->{$classArrayKey}()->save(new $class($modelData));

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }

        DB::commit();

        return $user;
    }


    public function client()
    {
        return $this->hasOne(Client::class);
    }


}
