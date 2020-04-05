<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHouseholdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('householders', function (Blueprint $table) {
            $table->bigIncrements('householder_id');
            $table->integer('account_id')->unique();
            $table->string('house_no', 30)
                ->nullable()
                ->default(null);
                
            $table->enum('type', ['owner', 'tenant']);
                
            $table->integer('block_id')
                ->nullable()
                ->default(null);
                
            $table->integer('lot_id')
                ->nullable()
                ->default(null);
                
            $table->mediumText('name')
                ->comment('json format first,last,middle,suffix')
                ->nullable()
                ->default(null);
                
            $table->string('contact_no')
                ->nullable()
                ->default(null);
                
            $table->date('moved_in')
                ->nullable()
                ->default(null);
            
            $table->string('remarks')
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
        Schema::dropIfExists('householders');
    }
}
