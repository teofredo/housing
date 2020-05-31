<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternetSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internet_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('subscription_id');
            $table->integer('account_id');
            $table->integer('plan_id');
            $table->tinyInteger('installed')->default(0);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
                
            $table->dateTime('cancelled_at')
                ->nullable()
                ->default(null);
                
            $table->string('cancel_reason')
                ->nullable()
                ->default(null);
                
            $table->tinyInteger('active')->default(1);
            $table->dateTime('installed_at')
                ->nullable()
                ->default(null);
                
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['account_id', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('internet_subscriptions');
    }
}
