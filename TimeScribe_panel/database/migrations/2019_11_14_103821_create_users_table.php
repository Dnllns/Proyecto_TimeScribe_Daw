<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            // USER DATA
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedInteger('workgroup_id')->nullable();
            $table->foreign('workgroup_id')->references('id')->on('workgroups')->onDelete('cascade');
            $table->unsignedInteger('is_admin')->nullable();
            $table->unsignedInteger('is_client')->nullable();



            //EXTRA
            $table->rememberToken();
            $table->timestamps();
            $table->timestamp('email_verified_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
