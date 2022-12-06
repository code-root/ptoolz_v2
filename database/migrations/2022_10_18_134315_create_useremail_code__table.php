<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUseremailCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('useremailcode', function (Blueprint $table) {
            $table->id();
            $table->string('code' , 40);
            $table->integer('attempts');
            $table->bigInteger('type');
            $table->dateTime('last_attempt');
            $table->text('user_Key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('useremail_code_');
    }
}
