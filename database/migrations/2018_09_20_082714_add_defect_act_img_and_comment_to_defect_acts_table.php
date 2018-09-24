<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefectActImgAndCommentToDefectActsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('defect_acts', function(Blueprint $table) {
            $table->string('defect_act_img')->after('defect_act_date')->nullable();
            $table->string('comment')->after('defect_act_img')->nullable();
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
            $table->dropColumn('defect_act_img');
            $table->dropColumn('comment');
        });
    }
}
