<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocksTable extends Migration
{
    /**
     * Run the migrations.
     * https://laravel.com/docs/5.7/migrations
     * @return void
     */
    public function up()
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->increments('id');
            $table->timestamps();
            $table->string('blockDate',15)->nullable($value = false);
            $table->string('signed',2)->nullable($value = false);
            $table->bigInteger('firstTransID')->nullable($value = false);
            $table->bigInteger('lastTransID')->nullable($value = false);
            $table->string('blockHash',64)->nullable($value = false);
            $table->string('parentHash',64)->nullable($value = false);
            $table->string('signatureBaaS',64)->nullable($value = true);
            $table->string('signature2',64)->nullable($value = true);
            $table->bigInteger('sig2MemberID')->nullable($value = true);
            $table->string('sig2Date',12)->nullable($value = true);
            $table->string('signature3',64)->nullable($value = true);
            $table->bigInteger('sig3MemberID')->nullable($value = true);
            $table->string('sig3Date',12)->nullable($value = true);
            $table->string('signature4',64)->nullable($value = true);
            $table->bigInteger('sig4MemberID')->nullable($value = true);
            $table->string('sig4Date',12)->nullable($value = true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blocks');
    }
}
