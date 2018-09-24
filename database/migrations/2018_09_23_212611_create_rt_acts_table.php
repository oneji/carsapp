<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRtActsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rt_acts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type');
            $table->string('company_from');
            $table->string('responsible_employee');
            $table->string('company_to');
            $table->integer('driver_id')->unsigned()->nullable();            
            $table->timestamps();

            $table->foreign('driver_id')->references('id')->on('drivers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rt_acts');
    }
}
