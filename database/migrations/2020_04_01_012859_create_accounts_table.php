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
            $table->string('account_no', 15)
                ->nullable()
                ->default(null);
                
            $table->integer('parent_id')
                ->nullable()
                ->default(null);
                
            $table->string('account_name')
                ->nullable()
                ->default(null);
                
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
                
            $table->enum('status', ['pending', 'active', 'inactive', 'deactivated', 'transferred'])->default('active');
            $table->dateTime('transferred_at')
                ->nullable()
                ->default(null);
                
            $table->integer('transferred_to')
                ->comment('account_id')
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
        Schema::dropIfExists('accounts');
    }
}
