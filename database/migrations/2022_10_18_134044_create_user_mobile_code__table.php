<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMobileCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     *
     * @return void
     *
     */
    /*
    Id	Big int
Code	varchar(40)
attempts	int
last attempt	dateTime
type	bigInt
userKey	text
 */
    public function up()
    {
        Schema::create('usermobilecode', function (Blueprint $table) {
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
        Schema::dropIfExists('user_mobile_code_');
    }
}
