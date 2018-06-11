<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefectOptionIdToServiceTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_types', function(Blueprint $table) {
            $table->integer('defect_option_id')->unsigned()->after('service_category_id');

            $table->foreign('defect_option_id')->references('id')->on('defect_options');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_types', function(Blueprint $table) {
            $table->dropColumn('defect_option_id');
        });
    }
}
