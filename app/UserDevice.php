<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{

    protected $table = 'users_devices';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid_device', 'id_adminsyf'
    ];

    public function user()
    {
        return $this->hasOne(User::class,'id_adminsyf','id_adminsyf');
    }
}
