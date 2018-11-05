<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('defect_id')->unsigned();
            $table->integer('defect_conclusion_id')->unsigned();
            $table->double('price')->default(0);
            $table->double('human_hour_price')->nullable();
            $table->timestamps();

            $table->foreign('defect_id')->references('id')->on('defects');
            $table->foreign('defect_conclusion_id')->references('id')->on('defect_conclusions');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_prices');
    }
}
