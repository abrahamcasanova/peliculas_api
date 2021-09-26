<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'customer_id',
        'girlfriend_name',
        'boyfriend_name',
        'place_or_residence',
        'status',
        'wedding_date',
        'number_of_nights',
        'type_of_ceremony',
        'number_of_guests',
        'wedding_budget',
        'wedding_budget_by_person',
        'schedule',
        'destination',
        'honeymoon',
        'comments'
      ];

      public function logs(){
          return $this->hasMany('App\Logs', 'quote_id', 'id');
      }
}
