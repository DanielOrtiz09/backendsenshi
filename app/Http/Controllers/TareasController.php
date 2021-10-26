<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MateriaDocente;
use App\Models\Tarea;
use Illuminate\Support\Facades\DB;

class TareasController extends Controller
{

    public function apieliminartarea(Request $request)
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

                    $idTarea = $request->idTarea;


                    $listaTarea = Tarea::where('id','=',$idTarea)
                    ->first();
                    if(!$listaTarea->delete()){
                        $mensaje ="No se eliminó la tarea, intenta más tarde";
                    }else{
                        $mensaje = "";
                        $validar = true;
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


    public function apifiltrartarea(Request $request)
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

                    $idTarea = $request->idTarea;


                    $listaTareas = DB::table('tareas')
                    ->join('materia_docentes','tareas.idMateriaDocente','=','materia_docentes.id')
                    ->join('docentes','materia_docentes.idDocente','=','docentes.id')
                    ->join('materias','materia_docentes.idMateria','=','materias.id')
                    ->select('tareas.*','docentes.nombreDocente','materias.nombreMateria')
                    ->where('tareas.estado','=',1)
                    ->where('tareas.idAsignarTipoUsuario','=',$idAsignarTipoUsuario)
                    ->where('tareas.id','=',$idTarea)
                    ->orderBy('tareas.fechaEntrega')
                    ->get();
                    $mensaje = "";
                    $validar = true;
                    
                    return response()->json([
                        'arrayTareas'=>$listaTareas,
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


     public function apicargartareas(Request $request)
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

                    $fechaActual = Date('Y-m-d');

                    $listaTareas = DB::table('tareas')
                    ->join('materia_docentes','tareas.idMateriaDocente','=','materia_docentes.id')
                    ->join('docentes','materia_docentes.idDocente','=','docentes.id')
                    ->join('materias','materia_docentes.idMateria','=','materias.id')
                    ->select('tareas.*','docentes.nombreDocente','materias.nombreMateria')
                    ->where('tareas.estado','=',1)
                    ->where('tareas.idAsignarTipoUsuario','=',$idAsignarTipoUsuario)
                    ->where('tareas.fechaEntrega','>=',$fechaActual)
                    ->orderBy('tareas.fechaEntrega')
                    ->get();
                    $mensaje = "";
                    $validar = true;
                    
                    return response()->json([
                        'arrayTareas'=>$listaTareas,
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

    public function apiguardartarea(Request $request)
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

                    $idMateriaDocente = $request->idMateriaDocente;
                    $nombreTarea = $request->nombreTarea;
                    $fechaEntrega = $request->fechaEntrega;
                    $horaEntrega = $request->horaEntrega;
                    $detalleTarea = $request->detalleTarea;

                     $date = strtotime($fechaEntrega);
                    $new_date = date('Y-m-d', $date);


                    $objTarea = new Tarea;
                    $objTarea->idMateriaDocente = $idMateriaDocente;
                    $objTarea->nombreTarea = $nombreTarea;
                    $objTarea->fechaEntrega = $new_date;
                    $objTarea->horaEntrega = $horaEntrega;
                    $objTarea->detalleTarea = $detalleTarea;
                    $objTarea->estado = 1;
                    $objTarea->idAsignarTipoUsuario = $idAsignarTipoUsuario;
                    if(!$objTarea->save()){
                        $mensaje = "No se guardó la tarea intenta más tarde";
                    }else{
                        $mensaje = "";
                        $validar = true;
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

    public function apicargarmaterias(Request $request)
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

                    /* $listaMateriaDocentes = DB::table('materia_docentes')
                    ->join('materias','materia_docentes.idMateria','=','materias.id')
                    ->join('docentes','materia_docentes.idDocente','=','docentes.id')
                    ->select('materia_docentes.id as idMateriaDocente','materias.id as idMateria','materias.nombreMateria','docentes.id as idDocente','docentes.nombreDocente')
                    ->where('materia_docentes.estado','=',1)
                    ->where('materia_docentes.idAsignarTipoUsuario','=',$idAsignarTipoUsuario)
                    ->orderBy('materias.nombreMateria')
                    ->get();*/

                    $listaMateriaDocentes = DB::table('horario_materia_docentes')
                    ->join('materia_docentes','horario_materia_docentes.idMateriaDocente','=','materia_docentes.id')
                    ->join('materias','materia_docentes.idMateria','=','materias.id')
                    ->join('docentes','materia_docentes.idDocente','=','docentes.id')
                    ->select('materia_docentes.id as idMateriaDocente','materias.id as idMateria','materias.nombreMateria','docentes.id as idDocente','docentes.nombreDocente')
                    ->where('materia_docentes.estado','=',1)
                    ->where('horario_materia_docentes.estado','=',1)
                    ->where('materia_docentes.idAsignarTipoUsuario','=',$idAsignarTipoUsuario)
                    ->where('horario_materia_docentes.idAsignarTipoUsuario','=',$idAsignarTipoUsuario)
                    ->groupBy('materia_docentes.id','materias.id','materias.nombreMateria','docentes.id','docentes.nombreDocente')
                    ->orderBy('materias.nombreMateria')
                    ->get();

                    $mensaje = "";
                    $validar = true;
                    return response()->json([
                        'arrayMateriaDocentes'=>$listaMateriaDocentes,
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
}
