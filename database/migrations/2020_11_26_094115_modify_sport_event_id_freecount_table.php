<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifySportEventIdFreecountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('free_trade_counts', function (Blueprint $table) {
            $table->string('sport_event_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('free_trade_counts', function (Blueprint $table) {
            $table->bigInteger('sport_event_id')->change();
        });
    }
}
