<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade', function (Blueprint $table) {
            $table->id();
            $table->string('sport_id');
            $table->string('sport_event_id');
            $table->string('season_id');
            $table->string('competition_id');
            $table->date('schedule_date')->nullable();
            $table->dateTime('sport_event_start_time');
            $table->json('competition');
            $table->json('sport_event_status');
            $table->longText('markets_2_way');
            $table->longText('competitors');
            $table->dateTime('generated_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trade');
    }
}
