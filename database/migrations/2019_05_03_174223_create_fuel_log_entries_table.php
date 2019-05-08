<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class CreateFuelLogEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fuel_log_entries', function (Blueprint $table) {
            # Create auto-increment primary key
            $table->bigIncrements('id');

            #generate created_at and updated_at
            $table->timestamps();

            #columns I added
            $table->date('fillup_date')->default(Carbon::now());;
            $table->float('fuel_volume');
            $table->string('fuel_units');
            $table->float('start_distance');
            $table->float('end_distance');
            $table->string('distance_units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fuel_log_entries');
    }
}
