<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MateriaDocente;
use App\Models\Dia;
use App\Models\Docente;
use App\Models\Materia;
use App\Models\HorarioMateriaDocente;
use Illuminate\Support\Facades\DB;

class HorarioController extends Controller
{

    public function apieliminarhorariomateria(Request $request)
    {
        $validar = false;
        $mensaje = "OCURRIÓ UN ERROR INESPERADO";
        try {

            $idAsignarTipoUsuario = $request->idAsignarTipoUsuario;            
            $idHorario = $request->idHorario;
            if(is_null($idAsignarTipoUsuario) || empty($idAsignarTipoUsuario)){
                $mensaje = "No tiene acceso a esta aplicación";
            }else if(is_null($idHorario) || empty($idHorario)){
                $mensaje = "El horario a eliminar no existe";
            }else{

                $listaHorario = HorarioMateriaDocente::where('estado','=',1)
                ->where('idAsignarTipoUsuario','=',$idAsignarTipoUsuario)
                ->where('id','=',$idHorario)
                ->first();
                if($listaHorario == null){
                    $mensaje ="El horario a eliminar no existe";
                }else{
                    if(!$listaHorario->delete()){
                        $mensaje ="No se eliminó el horario, intenta más tarde";
                    }else{
                        $mensaje = "";
                        $validar = true;   
                    }
                }
            }
        }catch (Exception $e) {
            $mensaje = "OCURRIÓ UN ERROR INESPERADO";
        }

        return response()->json([
            'mensaje'=>$mensaje,
            'validar'=>$validar
        ]);
    }

    public function apiguardarhorario(Request $request)
    {
        $validar = false;
        $mensaje = "OCURRIÓ UN ERROR INESPERADO";
        try {

            $idAsignarTipoUsuario = $request->idAsignarTipoUsuario;            
                
            if(is_null($idAsignarTipoUsuario) || empty($idAsignarTipoUsuario)){
                $mensaje = "No tiene acceso a esta aplicación";
            }else{
                

                $identificadorAlumno = config('global.idRolAlumno');

                $listaAsignarTipoUsuario = DB::table('asignar_tipo_usuarios')
                ->join('tipo_usuarios','asignar_tipo_usuarios.idTipoUsuario','=','tipo_usuarios.id')
                ->select('asignar_tipo_usuarios.*','tipo_usuarios.descripcion','tipo_usuarios.identificador','tipo_usuarios.descripcionTipoUsuario')
                ->where('asignar_tipo_usuarios.estado','=',1)
                ->where('tipo_usuarios.identificador','=',$identificadorAlumno)
                ->where('asignar_tipo_usuarios.id','=',$idAsignarTipoUsuario)
                ->get();

                if($listaAsignarTipoUsuario->count() == 0){
                    $mensaje = "No tiene acceso a esta aplicación";
                }else{
                    $idDia = $request->idDia;
                    $nombreDocente = mb_strtolower(trim($request->nombreDocente));
                    $nombreMateria = mb_strtolower(trim($request->nombreMateria));
                    $horaInicio = $request->horaInicio;
                    $horaFin = $request->horaFin;

                    $listaDocente = Docente::where('nombreDocente','=',$nombreDocente)
                    ->first();
                    $idDocente = 0;
                    if($listaDocente != null){
                        $idDocente = $listaDocente->id;
                    }else{
                        $objDocente = new Docente;
                        $objDocente->nombreDocente = $nombreDocente;
                        $objDocente->estado = 1;
                        $objDocente->idAsignarTipoUsuario = $idAsignarTipoUsuario;

                        $objDocente->save();
                        $idDocente = $objDocente->id;
                    }
                    $listaMateria = Materia::where('nombreMateria','=',$nombreMateria)
                    ->first();
                    $idMateria = 0;
                    if($listaMateria != null){
                        $idMateria = $listaMateria->id;
                    }else{
                        $objMateria = new Materia;
                        $objMateria->nombreMateria = $nombreMateria;
                        $objMateria->descripcionMateria = "";
                        $objMateria->estado = 1;
                        $objMateria->idAsignarTipoUsuario = $idAsignarTipoUsuario;

                        $objMateria->save();
                        $idMateria = $objMateria->id;
                    }

                    if($idDocente == 0 || $idMateria == 0){
                        $mensaje  ="No se guardó el registro intenta más tarde";
                    }else{

                    

                        $horaInicioComparar = strtotime($horaInicio);
                        $horaFinComparar = strtotime($horaFin);

                        if($horaInicioComparar >= $horaFinComparar) {
                            $mensaje  = "La hora fin no debe ser mayor o igual a la hora inicio";
                        }else{
                  
                            $listaHorario = HorarioMateriaDocente::where('idDia','=',$idDia)
                            ->whereBetween('horaInicio', [$horaInicio, $horaFin])
                            ->where('estado','=',1)
                            ->first();
                            $listaHorarioFin = HorarioMateriaDocente::where('idDia','=',$idDia)
                            ->whereBetween('horaFin', [$horaInicio, $horaFin])
                            ->where('estado','=',1)
                            ->first();



                            if($listaHorario != null){
                                $mensaje = "Ya existe una materia en el horario a ingresar, no deben existir conflictos en los horarios";
                            }else if($listaHorario != null){
                                $mensaje = "Ya existe una materia en el horario a ingresar, no deben existir conflictos en los horarios";
                            }else{

                                $listaMateriaDocente = MateriaDocente::where('idMateria','=',$idMateria)
                                ->where('idDocente','=',$idDocente)
                                ->where('estado','=',1)
                                ->where('idAsignarTipoUsuario','=',$idAsignarTipoUsuario)
                                ->first();

                                $idMateriaDocente = 0;
                                if($listaMateriaDocente != null){
                                    $idMateriaDocente = $listaMateriaDocente->id;
                                }else{
                                    $objMateriaDocente = new MateriaDocente;
                                    $objMateriaDocente->idDocente = $idDocente;
                                    $objMateriaDocente->idMateria = $idMateria;
                                    $objMateriaDocente->estado = 1;
                                    $objMateriaDocente->idAsignarTipoUsuario = $idAsignarTipoUsuario;
                                    $objMateriaDocente->save();

                                    $idMateriaDocente = $objMateriaDocente->id;
                                }

                                if($idMateriaDocente == 0){
                                    $mensaje  ="No se guardó el registro intenta más tarde";
                                }else{

                                    $objHorario = new HorarioMateriaDocente;
                                    $objHorario->idDia = $idDia;
                                    $objHorario->idMateriaDocente = $idMateriaDocente;
                                    $objHorario->horaInicio = $horaInicio;
                                    $objHorario->horaFin = $horaFin;
                                    $objHorario->estado = 1;
                                    $objHorario->idAsignarTipoUsuario = $idAsignarTipoUsuario;
                                    $objHorario->save();


                                    $mensaje = "";
                                    $validar = true;
                                    return response()->json([
                                        'arrayHorario'=>$listaHorario,
                                        'mensaje'=>$mensaje,
                                        'validar'=>$validar,
                                    ]);
                                }
                            }
                        }
                    }

                }
            }
        } catch (Exception $e) {
            $mensaje = "OCURRIÓ UN ERROR INESPERADO";
        }
        return response()->json([
            'mensaje'=>$mensaje,
            'validar'=>$validar,
        ]);
    }

    public function apicargardias(Request $request)
    {
        $validar = false;
        $mensaje = "OCURRIÓ UN ERROR INESPERADO";
        try {

            $idAsignarTipoUsuario = $request->idAsignarTipoUsuario;            
                
            if(is_null($idAsignarTipoUsuario) || empty($idAsignarTipoUsuario)){
                $mensaje = "No tiene acceso a esta aplicación";
            }else{
                

                $identificadorAlumno = config('global.idRolAlumno');

                $listaAsignarTipoUsuario = DB::table('asignar_tipo_usuarios')
                ->join('tipo_usuarios','asignar_tipo_usuarios.idTipoUsuario','=','tipo_usuarios.id')
                ->select('asignar_tipo_usuarios.*','tipo_usuarios.descripcion','tipo_usuarios.identificador','tipo_usuarios.descripcionTipoUsuario')
                ->where('asignar_tipo_usuarios.estado','=',1)
                ->where('tipo_usuarios.identificador','=',$identificadorAlumno)
                ->where('asignar_tipo_usuarios.id','=',$idAsignarTipoUsuario)
                ->get();

                if($listaAsignarTipoUsuario->count() == 0){
                    $mensaje = "No tiene acceso a esta aplicación";
                }else{
                    $listaDias = Dia::where('estado','=',1)
                    ->orderBy('identificador')
                    ->get();

                    $mensaje = "";
                    $validar = true;
                    return response()->json([
                        'arrayDias'=>$listaDias,
                        'mensaje'=>$mensaje,
                        'validar'=>$validar,
                    ]);

                }
            }
        } catch (Exception $e) {
            $mensaje = "OCURRIÓ UN ERROR INESPERADO";
        }
        return response()->json([
            'mensaje'=>$mensaje,
            'validar'=>$validar,
        ]);
    }

    
    public function apicargarhorarioalumno(Request $request)
    {
        $validar = false;
        $mensaje = "OCURRIÓ UN ERROR INESPERADO";
        try {

            $idAsignarTipoUsuario = $request->idAsignarTipoUsuario;            
                
            if(is_null($idAsignarTipoUsuario) || empty($idAsignarTipoUsuario)){
                $mensaje = "No tiene acceso a esta aplicación";
            }else{
                

                $identificadorAlumno = config('global.idRolAlumno');

                $listaAsignarTipoUsuario = DB::table('asignar_tipo_usuarios')
                ->join('tipo_usuarios','asignar_tipo_usuarios.idTipoUsuario','=','tipo_usuarios.id')
                ->select('asignar_tipo_usuarios.*','tipo_usuarios.descripcion','tipo_usuarios.identificador','tipo_usuarios.descripcionTipoUsuario')
                ->where('asignar_tipo_usuarios.estado','=',1)
                ->where('tipo_usuarios.identificador','=',$identificadorAlumno)
                ->where('asignar_tipo_usuarios.id','=',$idAsignarTipoUsuario)
                ->get();

                if($listaAsignarTipoUsuario->count() == 0){
                    $mensaje = "No tiene acceso a esta aplicación";
                }else{


                    $listaDiasAlumno = DB::table('materia_docentes')
                   // ->join('materia_docentes','materia_docente_alumnos.idMateriaDocente','=','materia_docentes.id')
                    ->join('horario_materia_docentes','horario_materia_docentes.idMateriaDocente','=','materia_docentes.id')
                    ->join('dias','horario_materia_docentes.idDia','=','dias.id')
                    ->select('dias.id as idDia','dias.nombreDia','dias.identificador')
                    ->where('materia_docentes.estado','=',1)
                    ->where('materia_docentes.idAsignarTipoUsuario','=',$idAsignarTipoUsuario)
                    ->groupBy('dias.id','dias.nombreDia','dias.identificador')
                    ->orderBy('dias.identificador')
                    ->get();

                   /* $listaMateriasAlumno = DB::table('materia_docente_alumnos')
                    ->where('estado','=',1)
                    ->where('idAsignarTipoUsuario','=',$idAsignarTipoUsuario)
                    ->get();*/

                    $arrayHorario = array();

                    foreach ($listaDiasAlumno as $keyDias => $valueDias) {

                         $listaMateriasAlumno = DB::table('materia_docentes')
                        //->join('materia_docentes','materia_docente_alumnos.idMateriaDocente','=','materia_docentes.id')
                        ->join('materias','materia_docentes.idMateria','=','materias.id')
                        ->join('horario_materia_docentes','horario_materia_docentes.idMateriaDocente','=','materia_docentes.id')
                        ->join('docentes','materia_docentes.idDocente','=','docentes.id')
                        //->join('dias','horario_materia_docentes.idDia','=','dias.id')
                        ->select('horario_materia_docentes.id','materias.id as idMateria','materias.nombreMateria','horario_materia_docentes.horaInicio','horario_materia_docentes.horaFin','docentes.nombreDocente')
                        ->where('materia_docentes.estado','=',1)
                        ->where('horario_materia_docentes.idDia','=',$valueDias->idDia)
                        ->where('materia_docentes.idAsignarTipoUsuario','=',$idAsignarTipoUsuario)
                        ->where('horario_materia_docentes.estado','=',1)
                        //->where('materias.estado','=',1)
                        ->orderBy('horario_materia_docentes.horaInicio')
                        ->get();

                        
                        $arrayHorario[$keyDias] = [
                            'idDia'=>$valueDias->idDia,
                            'nombreDia'=>$valueDias->nombreDia,
                            'arrayMaterias'=>$listaMateriasAlumno
                        ];
                    }

                    $mensaje = "";
                    $validar = true;

                    
                    return response()->json([
                        'arrayHorario'=>$arrayHorario,
                        'mensaje'=>$mensaje,
                        'validar'=>$validar,
                    ]);
  
                }
            }
        } catch (Exception $e) {
            $mensaje = "OCURRIÓ UN ERROR INESPERADO";
        }

        return response()->json([
            'mensaje'=>$mensaje,
            'validar'=>$validar
        ]);
    }
}
