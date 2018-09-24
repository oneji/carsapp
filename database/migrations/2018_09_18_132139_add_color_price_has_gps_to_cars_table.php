<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColorPriceHasGpsToCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cars', function(Blueprint $table) {
            $table->string('color')->nullable()->after('number');
            $table->integer('price')->nullable()->after('color');
            $table->integer('has_gps')->default(0)->after('price');
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
            $table->dropColumn('color');
            $table->dropColumn('price');
            $table->dropColumn('has_gps');
        });
    }
}
