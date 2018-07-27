<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRepairCommentToRepairRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repair_requests', function(Blueprint $table) {
            $table->text('repair_comment')->nullable()->after('repair_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('repair_requests', function(Blueprint $table) {
            $table->dropColumn('repair_comment');
        });
    }
}
