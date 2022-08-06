<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transport_team', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('team_id');
            $table->foreign('team_id')->references('id')->on('teams')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('transport_id');
            $table->foreign('transport_id')->references('id')->on('transports')->onUpdate('cascade')->onDelete('cascade');

            $table->timestamp('cooldown_marketing')->nullable();

            $table->integer('amount_have');
            $table->integer('use_num')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transport_team');
    }
}
