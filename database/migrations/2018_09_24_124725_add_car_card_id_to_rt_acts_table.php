<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCarCardIdToRtActsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rt_acts', function(Blueprint $table) {
            $table->integer('car_card_id')->unsigned()->after('type');
            $table->foreign('car_card_id')->references('id')->on('car_cards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rt_acts', function(Blueprint $table) {
            $table->dropColumn('car_card_id');
        });
    }
}
