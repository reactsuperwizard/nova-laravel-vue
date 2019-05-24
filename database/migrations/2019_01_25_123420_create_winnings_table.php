<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWinningsTable extends Migration
{
    /**
     * Run the migrations.
     * https://laravel.com/docs/5.7/migrations
     * @return void
     */
    public function up()
    {
        Schema::create('winnings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->increments('id');
            $table->timestamps();
            $table->bigInteger('transactionID')->nullable($value = false);
            $table->string('result',30)->nullable($value = false);
            $table->string('class',10)->nullable($value = true);
            $table->decimal('prize',8,2)->nullable($value = false);
            $table->string('reference',50)->nullable($value = true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('winnings');
    }
}
