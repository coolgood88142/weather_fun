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
            $table->string('city', 30);
            $table->string('temperature', 30);
            $table->Integer('probability_of_precipitation');
            $table->string('anemometer', 30);
            $table->string('barometric_pressure', 30);
            $table->Integer('relative_humidity');
            $table->Integer('ultraviolet_index');
            $table->string('seismicity', 30);
            $table->string('small_area_seismicity', 30);
            $table->Integer('acid_rain_ph');
            $table->string('sunrise', 30);
            $table->string('moonrise', 30);
            $table->Integer('ozone_year_avg');
            $table->string('next_week_weather', 50);

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
        Schema::dropIfExists('weather');
    }
}
