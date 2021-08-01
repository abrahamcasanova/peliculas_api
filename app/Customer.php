<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $hidden = array('deleted_at');

    protected $fillable = [
        'name',
        'last_name',
        'phone',
        'cellphone_boyfriend',
        'cellphone_girlfriend',
        'emails',
        'type_contact',
        'status'
      ];
  
      public function quote()
    {
        return $this->belongsTo(Quote::class,'id','customer_id');
    }
}
