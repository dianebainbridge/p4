<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class CreateFuelLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fuel_logs', function (Blueprint $table) {
            # Create auto-increment primary key
            $table->bigIncrements('id');

            # This generates two columns: `created_at` and `updated_at` to
            # keep track of changes to a row
            $table->timestamps();

            #columns I added
            $table->date('fill-up_date')->default(Carbon::now());;
            $table->float('fuel_volume');
            $table->string('fuel_units');
            $table->float('distance');
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
        Schema::dropIfExists('fuel_logs');
    }
}
