<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRtActChecklistItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rt_act_checklist_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_name');
            $table->integer('rt_act_checklist_id')->unsigned();

            $table->foreign('rt_act_checklist_id')->references('id')->on('rt_act_checklists');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rt_act_checklist_items');
    }
}
