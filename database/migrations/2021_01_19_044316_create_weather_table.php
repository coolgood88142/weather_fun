<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeatherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weather', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('city', 20);
            $table->string('temperature', 20);
            $table->Integer('probability_of_precipitation');
            $table->Integer('wind_direction');
            $table->string('anemometer', 20);
            $table->string('barometric_pressure', 100);
            $table->string('relative_humidity', 20);
            $table->Integer('ultraviolet_index');
            $table->string('seismicity', 100);
            $table->string('small_area_seismicity', 100);
            $table->Integer('acid_rain_ph');
            $table->string('sunrise', 100);
            $table->string('moonrise', 100);
            $table->Integer('ozone_year_avg');
            $table->string('next_week_weather', 100);
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
        Schema::dropIfExists('weather');
    }
}
