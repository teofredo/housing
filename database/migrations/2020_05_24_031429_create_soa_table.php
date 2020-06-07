<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('soa', function (Blueprint $table) {
            $table->increments('soa_id');
            $table->string('soa_no', 20);
            $table->integer('account_id');
            $table->string('due_date', 10);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['soa_no', 'account_id', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('soa');
    }
}
