<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarCardDefectOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_card_defect_option', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('car_card_id')->unsigned();
            $table->integer('defect_option_id')->unsigned();

            $table->foreign('car_card_id')->references('id')->on('car_cards');
            $table->foreign('defect_option_id')->references('id')->on('defect_options');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_card_defect_option', function(Blueprint $table) {
            $table->dropIfExists('car_card_defect_option');
        });
    }
}
