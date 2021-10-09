<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeagueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('league', function (Blueprint $table) {
            $table->id();
            $table->string('name',255);
            $table->integer('founder');
            $table->string('league_type')->nullable();
            $table->string('password',255)->nullable();
            $table->string('no_of_memebers',255)->nullable();
            $table->integer('portfolio_value')->nullable();
            $table->integer('duration')->nullable();
            $table->string('image',255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('league');
    }
}
