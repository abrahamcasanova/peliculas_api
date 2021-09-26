<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovieSchedule extends Model
{
    protected $table = 'movies_schedules';

    protected $fillable = [
        'movie_id', 'schedule_id',
    ];
}
