<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RtActRtActChecklistItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rt_act_rt_act_checklist_item', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('rt_act_checklist_item_id')->unsigned();
            $table->integer('rt_act_id')->unsigned();
            $table->integer('status');
            $table->string('comment');

            $table->foreign('rt_act_checklist_item_id')->references('id')->on('rt_act_checklist_items');
            $table->foreign('rt_act_id')->references('id')->on('rt_acts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rt_act_rt_act_checklist_item');
    }
}
