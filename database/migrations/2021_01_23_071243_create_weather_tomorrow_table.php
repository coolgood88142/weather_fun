<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeatherTomorrowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weather_tomorrow', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('city', 20);
            $table->string('temperature', 20);
            $table->Integer('probability_of_precipitation');
            $table->Integer('time_period');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weather_tomorrow');
    }
}
