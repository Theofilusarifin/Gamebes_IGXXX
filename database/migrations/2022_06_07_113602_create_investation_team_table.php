<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestationTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investation_team', function (Blueprint $table) {
            $table->foreignId('team_id');
            $table->foreign('team_id')->references('id')->on('teams')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('investation_id');
            $table->foreign('investation_id')->references('id')->on('investations')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('total_profit')->nullable();

            $table->tinyInteger('start')->nullable();
            $table->tinyInteger('finish')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('investation_team');
    }
}
