<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceToDefectDefectConclusionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('defect_defect_conclusion', function(Blueprint $table) {
            $table->double('service_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('defect_defect_conclusion', function(Blueprint $table) {
            $table->dropColumn('service_price');
        });
    }
}
