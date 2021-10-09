<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameLeagueUseridInvitationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('league_invitations', function (Blueprint $table) {
            $table->renameColumn('users_id', 'mobile_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('league_invitations', function (Blueprint $table) {
            $table->renameColumn('mobile_number', 'users_id');
        });
    }
}
