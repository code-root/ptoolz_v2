<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceproviderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    /*
 Id	Big int
Username	varchar(15)
Email	varchar(40)
mobile	varchar(20)
password	varchar(40)
User_key 	text
activity	tinyInt
Category_Id	int
account_type_Id	tinyInt
created at	dateTime
 */
    public function up()
    {
        Schema::create('serviceprovider', function (Blueprint $table) {
            $table->id();
            $table->string('username',15);
            $table->text('full_name');
            $table->string('email',40);
            $table->string('password',40);
            $table->string('mobile',40);
            $table->text('user_key');
            $table->boolean('activity')->default(0);
            $table->bigInteger('category_Id');
            $table->integer('account_type_Id');
            $table->dateTime('created_at')->default(now());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('serviceprovider');
    }
}
