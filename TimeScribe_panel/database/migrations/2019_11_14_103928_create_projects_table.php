<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Project;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {

            $table->increments('id');

            //ID CLIENTE
            $table->unsignedInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');

            //NOMBRE
            $table->string('name', 50);

            //DESCRIPCION
            $table->string('description', 250);

            //ESTADO
            $table->tinyInteger('status')->default(Project::STATUS_TODO);

            //VISIBLE
            $table->tinyInteger('visible')->default(Project::VISIBLE);

            //Workgroup
            $table->unsignedInteger('workgroup_id')->nullable();
            $table->foreign('workgroup_id')->references('id')->on('workgroups')->onDelete('cascade');

            //FECHA DE INICIO
            $table->dateTime('start_date')->nullable();

            //FECHA DE FINALIZACION
            $table->dateTime('finish_date')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }




}
