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
            $table->float('prev_read')->default(0);
            $table->float('curr_read');
            $table->dateTime('prev_read_date')
                ->nullable()
                ->default(null);

            $table->dateTime('curr_read_date');
            $table->string('due_date', 10);
            $table->decimal('rate_applied');
            $table->tinyInteger('is_minimum')->default(0);
            $table->string('read_by', 30)
                ->nullable()
                ->default(null);

            $table->timestamps();
            
            $table->unique(['account_id', 'due_date']);
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
