<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->bigIncrements('resident_id');
            $table->integer('account_id')->unique();
            $table->string('house_no', 30)->unique();
                
            $table->integer('block_id')
                ->nullable()
                ->default(null);
                
            $table->integer('lot_id')
                ->nullable()
                ->default(null);
                
            $table->string('name')
                ->nullable()
                ->default(null);
                
            $table->string('email')
                ->nullable()
                ->default(null);
                
            $table->string('contact_no')
                ->nullable()
                ->default(null);
                
            $table->date('moved_in')
                ->nullable()
                ->default(null);
                                
            $table->enum('active', [0, 1])->default(1);
            $table->mediumText('remarks');
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['block_id', 'lot_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('residents');
    }
}
