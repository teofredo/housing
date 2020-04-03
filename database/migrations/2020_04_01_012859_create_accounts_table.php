<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('account_id');
            $table->string('account_no', 12);
            $table->integer('parent_id')
                ->nullable()
                ->default(null);
                
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename');
            $table->string('suffix');
            $table->string('email', 100);
            
            $table->string('username', 100)
                ->nullable()
                ->default(null);
                
            $table->string('password')
                ->nullable()
                ->default(null);
                
            $table->dateTime('activated_at')
                ->nullable()
                ->default(null);
                
            $table->enum('active', [0, 1])->default(0);
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
        Schema::dropIfExists('accounts');
    }
}
