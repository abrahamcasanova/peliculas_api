<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at']; //Registramos la nueva columna
    protected $appends = ['name_type_task'];

    protected $fillable = [
      'name',
      'user_id',
      'type_task',
      'insured_name',
      'insurance_id',
      'policy_number',
      'name_contractor',
      'price',
      'actual_price',
      'renov_price',
      'reservation',
      'amount_paid',
      'agent',
      'sub_agent',
      'branch_id',
      'sub_branch_id',
      'type_endoso',
      'recibe',
      'is_subsequent',
      'is_paid',
      'number_endorsement',
      'method_of_payment',
      'type_of_currency',
      'suffering',
      'date_init',
      'final_date',
      'limit_date_paid',
      'last_reminder',
      'day_payment',
      'date_sinister',
      'date_paid',
      'payment_applied',
      'sent_invoice',
      'cancelled',
      'sinister_number',
      'responsable',
      'status',
      'comments',
    ];

    public function insurance()
    {
        return $this->hasOne(Insurance::class,'id','insurance_id');
    }

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function branches()
    {
        return $this->hasOne(Branch::class,'id','branch_id');
    }

    public function subBranches()
    {
        return $this->hasOne(SubBranch::class,'id','sub_branch_id');
    }

    public function getNameTypeTaskAttribute()
    {
        $name = null;
        switch ($this->type_task) {
            case 1:
                $name = 'Cotización';
                break;
            case 2:
                $name = 'Renovación';
                break;
            case 3:
                $name = 'Endoso';
                break;
            case 4:
                $name = 'Cobranza';
                break;
            case 5:
                $name = 'Siniestro';
                break;
            default:
                $name = 'Sin nombre';
                break;
        }
        return $name;
    }
}
