<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyDuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_dues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');
            $table->integer('account_id');
            $table->date('due_date');
            $table->decimal('amount_due');
            $table->mediumText('data');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['code', 'account_id', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monthly_dues');
    }
}
