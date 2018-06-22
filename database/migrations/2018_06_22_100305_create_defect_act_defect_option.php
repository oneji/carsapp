<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefectActDefectOption extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defect_act_defect_option', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('defect_act_id')->unsigned();
            $table->integer('defect_option_id')->unsigned();

            $table->foreign('defect_act_id')->references('id')->on('defect_acts');
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
        Schema::table('defect_act_defect_option', function(Blueprint $table) {
            $table->dropIfExists('defect_act_defect_option');
        });
    }
}
