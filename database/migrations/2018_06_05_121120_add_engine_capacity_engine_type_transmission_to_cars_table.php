<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEngineCapacityEngineTypeTransmissionToCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cars', function(Blueprint $table) {
            $table->double('engine_capacity')->nullable();
            $table->integer('engine_type_id')->unsigned()->nullable();
            $table->integer('transmission_id')->unsigned()->nullable();

            $table->foreign('engine_type_id')->references('id')->on('engine_types');
            $table->foreign('transmission_id')->references('id')->on('transmissions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['engine_capacity', 'engine_type', 'transmission']);
        });
    }
}
