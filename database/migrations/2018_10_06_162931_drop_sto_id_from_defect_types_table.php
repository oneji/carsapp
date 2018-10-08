<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropStoIdFromDefectTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('defect_types', function(Blueprint $table) {
            $table->dropColumn('sto_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('defect_types', function(Blueprint $table) {
            $table->integer('sto_id')->unsigned()->nullable();
            $table->foreign('sto_id')->references('id')->on('stos');
        });
    }
}
