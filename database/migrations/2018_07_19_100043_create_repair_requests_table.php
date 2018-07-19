<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepairRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repair_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('car_id')->unsigned();
            $table->integer('company_id')->unsigned();
            $table->integer('sto_id')->unsigned();
            $table->text('comment');
            $table->integer('status')->default(0);
            $table->date('receive_date')->nullable(); 
            $table->timestamps();

            $table->foreign('car_id')->references('id')->on('cars');
            $table->foreign('company_id')->references('id')->on('companies');
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
        Schema::dropIfExists('repair_requests');
    }
}
