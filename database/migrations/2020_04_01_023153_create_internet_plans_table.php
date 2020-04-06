<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternetPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internet_plans', function (Blueprint $table) {
            $table->increments('plan_id');
            $table->string('name');
            $table->decimal('monthly', 10, 2);
            $table->float('mbps', 10, 2);
            $table->mediumText('description')
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
        Schema::dropIfExists('internet_plans');
    }
}