<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarCardConsumableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_card_consumable', function(Blueprint $table) {
            $table->date('change_date')->nullable();
            $table->string('change_date_milage')->nullable();
            $table->string('recommended_change_milage')->nullable();
            $table->integer('car_card_id')->unsigned();
            $table->integer('consumable_id')->unsigned();

            $table->foreign('car_card_id')->references('id')->on('car_cards');
            $table->foreign('consumable_id')->references('id')->on('consumables');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
