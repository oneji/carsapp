<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefectDoneActTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defect_done_act', function(Blueprint $table) {
            $table->integer('defect_id')->unsigned();
            $table->integer('done_act_id')->unsigned();
            $table->integer('done')->default(0);

            $table->foreign('defect_id')->references('id')->on('defects');
            $table->foreign('done_act_id')->references('id')->on('done_acts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('defect_done_act');
    }
}
