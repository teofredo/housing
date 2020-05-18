<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process', function (Blueprint $table) {
            $table->bigIncrements('process_id');
            $table->string('code')
                ->nullable()
                ->default(null)
                ->comment('process group code');

            $table->tinyInteger('order')
                ->nullable()
                ->default(null);
                
            $table->string('name');
            $table->mediumText('data')
                ->nullable()
                ->default(null);

            $table->string('due_date', 10)
                ->nullable()
                ->default(null);

            $table->enum('status', ['pending','processing','done','failed'])->default('pending');
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
        Schema::dropIfExists('process');
    }
}
