<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefectConclusionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defect_conclusions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('conclusion_name');
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
        Schema::dropIfExists('defect_conclusions');
    }
}
