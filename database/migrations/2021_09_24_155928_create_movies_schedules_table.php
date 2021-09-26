<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('movie_id')->nullable();
            $table->unsignedInteger('schedule_id')->nullable();
            $table->timestamps();

            $table->foreign('movie_id')->references('id')->on('movies')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('schedule_id')->references('id')->on('schedules')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movies_schedules');
    }
}
