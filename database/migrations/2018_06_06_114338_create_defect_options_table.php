<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefectOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defect_options', function (Blueprint $table) {
            $table->increments('id');
            $table->string('defect_option_name');
            $table->integer('defect_id')->unsigned();

            $table->foreign('defect_id')->references('id')->on('defects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('defect_options');
    }
}
