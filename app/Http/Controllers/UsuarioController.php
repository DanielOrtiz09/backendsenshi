<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\TipoUsuario;
use App\Models\AsignarTipoUsuario;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
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
