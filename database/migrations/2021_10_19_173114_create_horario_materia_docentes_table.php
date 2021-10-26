<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorarioMateriaDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horario_materia_docentes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('idMateriaDocente');
            $table->foreign('idMateriaDocente')->references('id')->on('materia_docentes')->onDelete('cascade');
            $table->unsignedBigInteger('idDia');
            $table->foreign('idDia')->references('id')->on('dias')->onDelete('cascade');
            $table->time("horaInicio");
            $table->time("horaFin");
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
        Schema::dropIfExists('horario_materia_docentes');
    }
}
