<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->increments('fee_id');
            $table->string('code')
                ->nullable()
                ->default(null);

            $table->string('name');
            $table->decimal('fee', 10, 2);
            $table->tinyInteger('other_fee')->default(0);
            $table->mediumText('description')
                ->nullable()
                ->default(null);

            $table->tinyInteger('deleble')->default(1);
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
        Schema::dropIfExists('fees');
    }
}
