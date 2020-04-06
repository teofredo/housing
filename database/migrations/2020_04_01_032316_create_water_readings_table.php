<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaterReadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('water_readings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('account_id');
            $table->string('meter_no', 30);
            $table->float('prev_read');
            $table->float('curr_read');
            $table->dateTime('prev_read_date');
            $table->dateTime('curr_read_date');
            $table->date('due_date');
            $table->integer('reader_id');
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
        Schema::dropIfExists('water_readings');
    }
}
