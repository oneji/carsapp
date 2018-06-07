<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_equipment', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('car_id')->unsigned();
            $table->integer('equipment_type_id')->unsigned();

            $table->foreign('car_id')->references('id')->on('cars');
            $table->foreign('equipment_type_id')->references('id')->on('equipment_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_equipment');
    }
}
