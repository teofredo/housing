<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('payment_id');
            $table->string('code', 30)
                ->nullable()
                ->default(null);

            $table->integer('account_id');
            $table->string('reference_no')
                ->nullable()
                ->default(null);
                
            $table->decimal('amount_due')->default(0);
            $table->decimal('amount_received')->default(0);
            $table->decimal('amount_paid')->default(0);
            $table->decimal('current_balance')->default(0);
            $table->string('due_date', 10);
            $table->tinyInteger('other_payment')
                ->default(0)
                ->comment('not included in month due, recording only');

            $table->dateTime('paid_at')
                ->nullable()
                ->default(null);

            $table->string('description')
                ->nullable()
                ->default(null);

            $table->timestamps();
            $table->unique(['account_id', 'due_date', 'reference_no']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
