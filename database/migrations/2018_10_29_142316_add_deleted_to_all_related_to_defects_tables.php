<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedToAllRelatedToDefectsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('defect_types', function(Blueprint $table) {
            $table->integer('deleted')->default(0);
        });

        Schema::table('defects', function(Blueprint $table) {
            $table->integer('deleted')->default(0);
        });
        
        Schema::table('defect_conclusions', function(Blueprint $table) {
            $table->integer('deleted')->default(0);
        });

        Schema::table('defect_options', function(Blueprint $table) {
            $table->integer('deleted')->default(0);
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
            $table->dropColumn('deleted');
        });
        
        Schema::table('defects', function(Blueprint $table) {
            $table->dropColumn('deleted');
        });

        Schema::table('defect_conclusions', function(Blueprint $table) {
            $table->dropColumn('deleted');
        });

        Schema::table('defect_options', function(Blueprint $table) {
            $table->dropColumn('deleted');
        });
    }
}
