<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyStoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_sto', function(Blueprint $table) {
            $table->integer('company_id')->unsigned();
            $table->integer('sto_id')->unsigned();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('sto_id')->references('id')->on('stos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_sto');
    }
}
