<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login','App\Http\Controllers\UsuarioController@apilogin');
Route::post('logintipousuario','App\Http\Controllers\UsuarioController@apilogintipousuario');

Route::post('cargarhorarioalumno','App\Http\Controllers\HorarioController@apicargarhorarioalumno');
Route::post('cargardias','App\Http\Controllers\HorarioController@apicargardias');
Route::post('guardarhorario','App\Http\Controllers\HorarioController@apiguardarhorario');
Route::post('eliminarhorariomateria','App\Http\Controllers\HorarioController@apieliminarhorariomateria');



//TAREAS
Route::post('cargarmaterias','App\Http\Controllers\TareasController@apicargarmaterias');
Route::post('guardartarea','App\Http\Controllers\TareasController@apiguardartarea');
Route::post('cargartareas','App\Http\Controllers\TareasController@apicargartareas');
Route::post('filtrartarea','App\Http\Controllers\TareasController@apifiltrartarea');
Route::post('eliminartarea','App\Http\Controllers\TareasController@apieliminartarea');