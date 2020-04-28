<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table){
            $table->string('username')
                ->unique()
                ->after('name');
                
            $table->string('email')
                ->nullable()
                ->default(null)
                ->after('username')
                ->change();
                
            $table->enum('user_type', ['superadmin','admin','report','water-reader'])
                ->nullable()
                ->default(null)
                ->after('remember_token');
                
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
        Schema::table('users', function(Blueprint $table){
            $table->dropSoftDeletes();
            $table->string('email')
                ->unique()
                ->change();
                
            $table->dropColumn(['username', 'user_type']);
        });
    }
}
