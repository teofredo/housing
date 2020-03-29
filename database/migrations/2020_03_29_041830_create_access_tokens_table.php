<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')
                ->nullable()
                ->default(null);
                
            $table->mediumText('access_token');
            $table->mediumText('refresh_token')
                ->nullable()
                ->default(null);
                
            $table->dateTime('expired_at')
                ->nullable()
                ->default(null);
                
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
        Schema::dropIfExists('access_tokens');
    }
}
