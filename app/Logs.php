<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Logs extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $hidden = array('deleted_at');

    protected $fillable = [
        'user_id',
        'quote_id',
        'date_contact',
        'comments',
        'status'
      ];

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
}
