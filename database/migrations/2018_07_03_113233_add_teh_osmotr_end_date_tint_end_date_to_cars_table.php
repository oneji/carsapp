<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTehOsmotrEndDateTintEndDateToCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cars', function(Blueprint $table) {
            $table->date('teh_osmotr_end_date')->nullable();
            $table->date('tint_end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cars', function(Blueprint $table) {
            $table->dropColumn('teh_osmotr_end_date');
            $table->dropColumn('tint_end_date');
        });
    }
}
