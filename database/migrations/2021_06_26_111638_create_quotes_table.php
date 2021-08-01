<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->unsigned();
            $table->string('girlfriend_name')->nullable();
            $table->string('boyfriend_name')->nullable();
            $table->string('place_or_residence')->nullable();
            $table->date('wedding_date')->nullable();
            $table->string('type_of_ceremony')->nullable();
            $table->string('number_of_nights')->nullable();
            $table->integer('number_of_guests')->nullable();
            $table->float('wedding_budget',8,2)->nullable();
            $table->float('wedding_budget_by_person',8,2)->nullable();
            $table->string('schedule')->nullable();
            $table->string('destination')->nullable();
            $table->string('honeymoon')->nullable();
            $table->string('comments')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotes');
    }
}
