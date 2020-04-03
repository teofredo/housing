<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaterRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('water_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('min_fee', 10, 2)
                ->nullable()
                ->default(null);
                
            $table->float('min_m3', 10, 2);
            $table->float('max_m3', 10, 2);
            $table->decimal('per_m3', 10, 2)
                ->nullable()
                ->default(null);
                
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
        Schema::dropIfExists('water_rates');
    }
}
