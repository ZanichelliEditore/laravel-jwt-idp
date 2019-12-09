<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('providers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('domain')->unique();
            $table->string('username');
            $table->string('password');
            $table->string('logoutUrl');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('providers');
    }
}
