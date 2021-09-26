<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'name', 'img', 'status', 'description'
    ];

    public function getStatusAttribute($value) {
        return $value == 1 ? true: false;
    }

    public function schedules() {
        return $this->belongsToMany(Schedule::class,MovieSchedule::class)->withPivot('id','movie_id');
    }
}
