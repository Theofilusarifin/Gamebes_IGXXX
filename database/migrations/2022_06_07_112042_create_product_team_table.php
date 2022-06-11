<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_team', function (Blueprint $table) {
            $table->foreignId('team_id');
            $table->foreign('team_id')->references('id')->on('teams')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('amount_have');
            $table->integer('amount_sold')->nullable();
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
        Schema::dropIfExists('product_team');
    }
}
