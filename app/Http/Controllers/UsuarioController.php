<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\TipoUsuario;
use App\Models\AsignarTipoUsuario;
use App\Models\Dia;

use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function apiregistrarusuario(Request $request)
    {
        $validar = false;
        $mensaje = "OCURRIÓ UN ERROR INESPERADO";
        try {
            $identificadorAlumno = config('global.idRolAlumno');
            $listaTipoUsuario = TipoUsuario::where('identificador','=',$identificadorAlumno)
            ->where('estado','=',1)
            ->first();
            $idTipoUsuario = 0;
            if($listaTipoUsuario == null){
                $objTipoUsuario = new TipoUsuario;
                $objTipoUsuario->descripcion = "ESTUDIANTE";
                $objTipoUsuario->identificador = 1;
                $objTipoUsuario->descripcionTipoUsuario = "Publica horarios y tareas";
                $objTipoUsuario->estado = 1;
                $objTipoUsuario->save();
                $idTipoUsuario = $objTipoUsuario->id;
            }else{
                $idTipoUsuario = $listaTipoUsuario->id;
            }

            $listaDias = Dia::where('estado','=',1)
            ->get();

            if($listaDias->count() != 7){
                $listaDiaDisponible = $listaDias->where('nombreDia','=','LUNES');
                if($listaDiaDisponible->count() == 0){
                    $objDia = new Dia;
                    $objDia->nombreDia = "LUNES";
                    $objDia->identificador = 1;
                    $objDia->estado = 1;
                    $objDia->save();
                }
                $listaDiaDisponible = $listaDias->where('nombreDia','=','MARTES');
                if($listaDiaDisponible->count() == 0){
                    $objDia = new Dia;
                    $objDia->nombreDia = "MARTES";
                    $objDia->identificador = 2;
                    $objDia->estado = 1;
                    $objDia->save();
                }
                $listaDiaDisponible = $listaDias->where('nombreDia','=','MIERCOLES');
                if($listaDiaDisponible->count() == 0){
                    $objDia = new Dia;
                    $objDia->nombreDia = "MIERCOLES";
                    $objDia->identificador = 3;
                    $objDia->estado = 1;
                    $objDia->save();
                }
                $listaDiaDisponible = $listaDias->where('nombreDia','=','JUEVES');
                if($listaDiaDisponible->count() == 0){
                    $objDia = new Dia;
                    $objDia->nombreDia = "JUEVES";
                    $objDia->identificador = 4;
                    $objDia->estado = 1;
                    $objDia->save();
                }
                $listaDiaDisponible = $listaDias->where('nombreDia','=','VIERNES');
                if($listaDiaDisponible->count() == 0){
                    $objDia = new Dia;
                    $objDia->nombreDia = "LUNES";
                    $objDia->identificador = 5;
                    $objDia->estado = 1;
                    $objDia->save();
                }
                $listaDiaDisponible = $listaDias->where('nombreDia','=','SÁBADO');
                if($listaDiaDisponible->count() == 0){
                    $objDia = new Dia;
                    $objDia->nombreDia = "SÁBADO";
                    $objDia->identificador = 6;
                    $objDia->estado = 1;
                    $objDia->save();
                }
                $listaDiaDisponible = $listaDias->where('nombreDia','=','DOMINGO');
                if($listaDiaDisponible->count() == 0){
                    $objDia = new Dia;
                    $objDia->nombreDia = "DOMINGO";
                    $objDia->identificador = 0;
                    $objDia->estado = 1;
                    $objDia->save();
                }

            }


            if($idTipoUsuario == 0){
                $mensaje = "Error interno de la aplicación, comunícate con el administrador";
            }else{



                $usuario = $request->usuario;
                $clave = $request->clave;
                $fechaNacimiento = $request->fechaNacimiento;
                
                    
                if(is_null($usuario) || empty($usuario)){
                    $mensaje = "Ingrese el usuario";
                }else if(is_null($clave) || empty($clave)){
                    $mensaje = "Ingrese la clave";
                }else if(is_null($fechaNacimiento) || empty($fechaNacimiento)){
                    $mensaje = "Ingrese la fecha de nacimiento";
                }else {
                    $usuario = trim($usuario);
                    $clave = trim($clave);
                        
                    $listaUsuario = Usuario::where('usuario','=',$usuario)
                    ->where('estado','=',1)
                    ->first();
                    if($listaUsuario != null){
                        $mensaje = "Ya existe un usuario llamado ".$usuario;
                    }else{


                        $objUsuario = new Usuario;
                        $objUsuario->primerNombre = "";
                        $objUsuario->segundoNombre = "";
                        $objUsuario->primerApellido = "";
                        $objUsuario->segundoApellido = "";
                        $objUsuario->usuario = $usuario;
                        $objUsuario->clave = $clave;
                        $objUsuario->fechaNacimiento = $fechaNacimiento;
                        $objUsuario->estado = 1;

                        if(!$objUsuario->save()){
                            $mensaje = "No se guardó el usuario, intenta más tarde";
                        }else{
                            $idUsuario = $objUsuario->id;
                            $objAsignarTipoUsuario = new AsignarTipoUsuario;
                            $objAsignarTipoUsuario->idUsuario = $idUsuario;
                            $objAsignarTipoUsuario->idTipoUsuario = $idTipoUsuario;
                            $objAsignarTipoUsuario->estado = 1;
                            if(!$objAsignarTipoUsuario->save()){
                                $mensaje = "No se guardó el usuario, intenta más tarde";
                                $objUsuario->delete();
                            }else{
                                $mensaje = "";
                                $validar = true;
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
            'validar'=>$validar
        ]);
        
    }

    public function apilogintipousuario(Request $request)
    {
        $validar = false;
        $mensaje = "OCURRIÓ UN ERROR INESPERADO";
        try {
           

            $idUsuario = $request->idUsuario;
            $idAsignarTipoUsuario = $request->idAsignarTipoUsuario;
            
                
            if(is_null($idUsuario) || empty($idUsuario)){
                $mensaje = "No tienes acceso a esta aplicación";
            }else if(is_null($idAsignarTipoUsuario) || empty($idAsignarTipoUsuario)){
                $mensaje = "No tienes acceso a esta aplicación";
            }else{
                $listaAsignarTipoUsuario = DB::table('asignar_tipo_usuarios')
                ->join('tipo_usuarios','asignar_tipo_usuarios.idTipoUsuario','=','tipo_usuarios.id')
                ->join('usuarios','asignar_tipo_usuarios.idUsuario','=','usuarios.id')
                ->select('asignar_tipo_usuarios.*','usuarios.estado as estadoUsuario','tipo_usuarios.descripcion','tipo_usuarios.identificador','tipo_usuarios.descripcionTipoUsuario')
                ->where('asignar_tipo_usuarios.estado','=',1)
                ->where('asignar_tipo_usuarios.idUsuario','=',$idUsuario)
                ->where('asignar_tipo_usuarios.id','=',$idAsignarTipoUsuario)
                ->first();

                if($listaAsignarTipoUsuario == null){
                    $mensaje = "No tiene acceso a esta aplicación";
                }else if($listaAsignarTipoUsuario->estadoUsuario == 0){
                    $mensaje = "Su usuario ha sido desactivado por mal uso de la aplicación";
                }else{      
                        
                    $arrayAsignarTipoUsuario = [
                        'idAsignarTipoUsuario'=>$idAsignarTipoUsuario,
                        'descripcion'=>$listaAsignarTipoUsuario->descripcion,
                        'descripcionTipoUsuario'=>$listaAsignarTipoUsuario->descripcionTipoUsuario,
                        'identificador'=>$listaAsignarTipoUsuario->identificador
                    ];  
                    $mensaje = "";
                    $validar = true;

                    
                    return response()->json([
                        'arrayAsignarTipoUsuario'=>$arrayAsignarTipoUsuario,
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

    public function apilogin(Request $request)
    {
        $validar = false;
        $mensaje = "OCURRIÓ UN ERROR INESPERADO";
        try {


            $usuario = $request->usuario;
            $clave = $request->clave;
            
                
            if(is_null($usuario) || empty($usuario)){
                $mensaje = "Ingrese el usuario";
            }else if(is_null($clave) || empty($clave)){
                $mensaje = "Ingrese la clave";
            }else {
                $usuario = trim($usuario);
                $clave = trim($clave);
                    
                $listaUsuario = Usuario::where('usuario','=',$usuario)
                ->where('estado','=',1)
                ->first();
                if($listaUsuario == null){
                    $mensaje = "Usuario o clave no son correctos";
                }else{


                    $claveUsuario = $listaUsuario->clave;

                    if($clave != $claveUsuario){
                        $mensaje = "Usuario o clave no son correctos";
                    }else{ 


                        $idUsuario = $listaUsuario->id;

                        $listaAsignarTipoUsuario = DB::table('asignar_tipo_usuarios')
                        ->join('tipo_usuarios','asignar_tipo_usuarios.idTipoUsuario','=','tipo_usuarios.id')
                        ->select('asignar_tipo_usuarios.*','tipo_usuarios.descripcion','tipo_usuarios.identificador','tipo_usuarios.descripcionTipoUsuario')
                        ->where('asignar_tipo_usuarios.estado','=',1)
                        ->where('asignar_tipo_usuarios.idUsuario','=',$idUsuario)
                        ->get();

                        if($listaAsignarTipoUsuario->count() == 0){
                            $mensaje = "No tiene acceso a esta aplicación";
                        }else{
                            $nombres = $listaUsuario->primerApellido." ".$listaUsuario->segundoApellido." ".$listaUsuario->primerNombre." ".$listaUsuario->segundoNombre;
  
                            $arrayUsuario =  [
                                'idUsuario' => $idUsuario,
                                'usuario'=>$listaUsuario->usuario,
                                'nombres'=>$nombres,
                                'fechaNacimiento'=>$listaUsuario->fechaNacimiento,
                            ];
                            
                            $arrayAsignarTipoUsuario = array();
                            foreach ($listaAsignarTipoUsuario as $key => $value) {
                                

                                $arrayAsignarTipoUsuario[$key] = [
                                    'idAsignarTipoUsuario'=>$value->id,
                                    'descripcion'=>$value->descripcion,
                                    'descripcionTipoUsuario'=>$value->descripcionTipoUsuario,
                                    'identificador'=>$value->identificador
                                ];  
                            }
                            $mensaje = "";
                            $validar = true;

                            
                            return response()->json([
                                'arrayAsignarTipoUsuario'=>$arrayAsignarTipoUsuario,
                                'arrayUsuario'=>$arrayUsuario,
                                'mensaje'=>$mensaje,
                                'validar'=>$validar,
                            ]);
                        }
                    }
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
