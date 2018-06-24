<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefectActEquipmentTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defect_act_equipment_type', function(Blueprint $table) {
            $table->integer('defect_act_id')->unsigned();
            $table->integer('equipment_type_id')->unsigned();

            $table->foreign('defect_act_id')->references('id')->on('defect_acts');
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
        Schema::dropIfExists('defect_act_equipment_type');
    }
}
