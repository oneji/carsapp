<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActiveToCarDriverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_driver', function(Blueprint $table) {
            $table->integer('active')->default(1);
            $table->date('start_date')->default(Carbon\Carbon::now());
            $table->date('end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_driver', function($table) {
            $table->dropColumn('active');
        });
    }
}
