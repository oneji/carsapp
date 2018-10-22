<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefectDefectOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defect_defect_option', function(Blueprint $table) {
            $table->integer('defect_id')->unsigned();
            $table->integer('defect_option_id')->unsigned();
            $table->integer('defect_act_id')->unsigned();

            $table->foreign('defect_id')->references('id')->on('defects');
            $table->foreign('defect_option_id')->references('id')->on('defect_options');
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
        Schema::dropIfExists('defect_defect_option');
    }
}
