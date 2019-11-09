<?php

// Cargando clases
use App\Http\Middleware\ApiAuthMiddleware;
//RUTAS DE PRUEBA

Route::get('/welcome', function () {
    return view('welcome');
});


Route::get('/', function () {
    return '<h1>Hola mundo con Laravel</h1>';
});

Route::get('/pruebas/{nombre?}', function($nombre){
    $texto = '<h2>Texto desde una ruta</h2>';
    $texto .=  'Nombre: '.$nombre;
   return view('pruebas', array(
    'texto' => $texto
   ));
}); 

Route::get('/animales','PruebasController@index');
Route::get('/testOrm','PruebasController@testOrm');

// RUTAS DEL API
    
    /* Metodos HTTP comunes

     *GET: conseguir datos o recursos
     *POST: Guardar datos o recursos o hacer logica desde un formulario
     *PUT: Actualizar datos o recursos
     *DELETE: Eliminar datos o recursos

     api rest -> get y post
     api restful -> get post put y delete.

    */
    //Rutas de prueba
    Route::get('/usuario/pruebas', 'UserController@pruebas');
    Route::get('/post/pruebas', 'PostController@pruebas');
    Route::get('/categoria/pruebas', 'CategoryController@pruebas');

    // Rutas del controlador de usuarios
    Route::post('/api/register', 'UserController@register');
    Route::post('/api/login', 'UserController@login');
    Route::put('/api/user/update', 'UserController@update');
    Route::post('/api/user/upload' ,'UserController@upload')->middleware(ApiAuthMiddleware::class);
    Route::get('/api/user/avatar/{filename}', 'UserController@getImage');
    Route::get('/api/user/detail/{id}', 'UserController@detail');

