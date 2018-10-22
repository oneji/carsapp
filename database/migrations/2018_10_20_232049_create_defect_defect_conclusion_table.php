<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefectDefectConclusionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defect_defect_conclusion', function(Blueprint $table) {
            $table->integer('defect_id')->unsigned();
            $table->integer('defect_conclusion_id')->unsigned();
            $table->integer('defect_act_id')->unsigned();

            $table->foreign('defect_id')->references('id')->on('defects');
            $table->foreign('defect_conclusion_id')->references('id')->on('defect_conclusions');
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
        Schema::dropIfExists('defect_defect_conclusion');
    }
}
