<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     * https://laravel.com/docs/5.7/migrations
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->increments('id');
            $table->timestamps();
            $table->bigInteger('blockID')->nullable($value = false);
            $table->string('entryID',20)->nullable($value=true);
            $table->string('entryType',10)->nullable($value=false);
            $table->string('providerID',20)->nullable($value = false);
            $table->string('customerID',20)->nullable($value = false);
            $table->string('customerHash',64)->nullable($value = false);
            $table->string('eventType',20)->nullable($value = false);
            $table->string('eventID',20)->nullable($value = false);
            $table->string('ticketID',20)->nullable($value = false);
            $table->decimal('stake',8,2)->nullable($value = false);
            $table->string('prediction',40)->nullable($value = true);
            $table->string('star1',5)->nullable($value = true);
            $table->string('star2',5)->nullable($value = true);
            $table->index('eventID');
            $table->index('ticketID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
