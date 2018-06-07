<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefectTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defect_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('defect_type_name');
            $table->integer('sto_id')->unsigned();

            $table->foreign('sto_id')->references('id')->on('stos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('defect_types');
    }
}
