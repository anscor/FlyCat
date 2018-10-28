<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchasers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account');
            $table->string('alias', 24);
            $table->text('password');
            $table->float('blance');
            $table->timestamp('register_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchasers');
    }
}
