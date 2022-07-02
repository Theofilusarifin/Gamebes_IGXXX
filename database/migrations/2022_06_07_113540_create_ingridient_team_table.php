<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngridientTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingridient_team', function (Blueprint $table) {
            $table->timestamp('expired_time')->primary();

            $table->foreignId('team_id');
            $table->foreign('team_id')->references('id')->on('teams')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('ingridient_id');
            $table->foreign('ingridient_id')->references('id')->on('ingridients')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('amount_have');
            $table->integer('amount_use')->nullable();
            $table->double('total')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingridient_team');
    }
}
