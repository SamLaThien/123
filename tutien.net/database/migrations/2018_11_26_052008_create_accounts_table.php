<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('account_id')->nullable();
            $table->string('account_name')->nullable();
            $table->longText('cookie')->nullable();
            $table->string('tai_san')->nullable();
            $table->string('progress')->nullable();
            $table->boolean('is_nsd')->default(false);
            $table->boolean('is_nopbac')->default(true);
            $table->boolean('is_dt')->default(true);
            $table->unsignedInteger('group')->default(1);
            $table->timestamps();
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
