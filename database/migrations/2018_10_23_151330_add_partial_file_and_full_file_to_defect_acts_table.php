<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPartialFileAndFullFileToDefectActsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('defect_acts', function(Blueprint $table) {
            $table->string('partial_file')->nullable()->after('comment');
            $table->string('full_file')->nullable()->after('partial_file');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('defect_acts', function(Blueprint $table) {
            $table->dropColumn('partial_file');
            $table->dropColumn('full_file');
        });
    }
}
