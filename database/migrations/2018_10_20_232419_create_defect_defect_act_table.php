<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefectDefectActTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defect_defect_act', function(Blueprint $table) {
            $table->integer('defect_id')->unsigned();
            $table->integer('defect_act_id')->unsigned();
            $table->integer('to_report')->default(1);

            $table->foreign('defect_id')->references('id')->on('defects');
            $table->foreign('defect_act_id')->references('id')->on('defect_acts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('defect_defect_act');
    }
}
