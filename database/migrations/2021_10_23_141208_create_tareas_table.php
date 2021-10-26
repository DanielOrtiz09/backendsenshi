<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTareasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('idMateriaDocente')->null();
            $table->foreign('idMateriaDocente')->references('id')->on('materia_docentes')->onDelete('cascade');
            $table->string('nombreTarea',50);
            $table->string('detalleTarea',500);
            $table->date('fechaEntrega');
            $table->time("horaEntrega");
            $table->boolean('estado');
            $table->unsignedBigInteger('idAsignarTipoUsuario');
            $table->foreign('idAsignarTipoUsuario')->references('id')->on('asignar_tipo_usuarios');
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
        Schema::dropIfExists('tareas');
    }
}
