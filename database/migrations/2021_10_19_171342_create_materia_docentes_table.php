<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMateriaDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materia_docentes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('idMateria');
            $table->foreign('idMateria')->references('id')->on('materias')->onDelete('cascade');
            $table->unsignedBigInteger('idDocente');
            $table->foreign('idDocente')->references('id')->on('docentes')->onDelete('cascade');
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
        Schema::dropIfExists('materia_docentes');
    }
}
