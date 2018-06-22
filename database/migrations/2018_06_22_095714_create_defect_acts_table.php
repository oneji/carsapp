<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefectActsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defect_acts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('car_card_id')->unsigned();
            $table->dateTime('defect_act_date');
            $table->timestamps();

            $table->foreign('car_card_id')->references('id')->on('car_cards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('defect_acts');
    }
}
