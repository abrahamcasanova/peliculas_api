<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_adminsyf', 'poliza', 'recibo','compania','contratante', 'comentario', 'fInicial', 'fFinal', 
            'diasVigor', 'primaTotal', 'moneda', 'formaPago', 'medioPago', 'pagado'
    ];

}
