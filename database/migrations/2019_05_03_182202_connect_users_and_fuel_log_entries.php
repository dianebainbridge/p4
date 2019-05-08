<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConnectUsersAndFuelLogEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('fuel_log_entries',function (Blueprint $table)
       {
           #add user_id field to table as foreign key
           $table->bigInteger('user_id')->unsigned();
           $table->foreign('user_id')->references('id')->on('users');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fuel_log_entries',function (Blueprint $table)
        {
            #add user_id field to table as foreign key
            $table->dropForeign('fuel_log-entries_user_id_foreign');
            $table->dropColumn('user_id');
        });
    }
}
