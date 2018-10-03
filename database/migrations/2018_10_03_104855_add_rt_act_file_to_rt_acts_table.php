<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRtActFileToRtActsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rt_acts', function(Blueprint $table) {
            $table->string('rt_act_file')->nullable()->after('driver_id');
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
            $table->dropColumn('rt_act_file');
        });
    }
}
