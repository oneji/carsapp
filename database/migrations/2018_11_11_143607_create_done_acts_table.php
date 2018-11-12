<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoneActsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('done_acts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('defect_act_id')->unsigned();
            $table->integer('car_card_id')->unsigned();
            $table->string('comment')->nullable();
            $table->integer('user_id')->unsigned();
            $table->integer('sent')->default('0');
            $table->integer('confirmed')->default('0');
            $table->integer('closed')->default('0');
            $table->timestamps();

            $table->foreign('defect_act_id')->references('id')->on('defect_acts');
            $table->foreign('car_card_id')->references('id')->on('car_cards');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('done_acts');
    }
}
