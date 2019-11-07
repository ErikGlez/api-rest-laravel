<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    public function pruebas(Request $request){
        return "Acción de pruebas de USER-CONTROLLER";
    }

    public function register(Request $request){
       
       // Recoger los datos del usuario por post
       $json = $request->input('json', null);

       //decodificar
       $params = json_decode($json); // objeto
       $params_array = json_decode($json, true); // array
        
       if(!empty($params) && !empty($params_array)){
       
            
                // Limpiar datos (espacios por delante y atras)
            $params_array = array_map('trim', $params_array);
            // Validar datos 
            // Comprobar si el usuario existe (duplicado)  -> unique
            $validate = \Validator::make($params_array, [
                    'name'       => 'required|alpha',
                    'surname'    => 'required|alpha',
                    'email'      => 'required|email|unique:users',  
                    'password'   => 'required'
            ]);

            if($validate->fails()){
                
                    $data = array(
                        'status' => 'error',
                        'code'   => 404,
                        'message' => 'El usuario no se ha creado',
                        'errors' =>  $validate->errors()
                    );
                
            }else{

                // Cifrar la contraseña
                $pwd = hash('sha256', $params->password);

                //Crear usuario
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';

                // Guardar el usuario
                $user->save();


                $data = array(
                    'status' => 'success',
                    'code'   => 200,
                    'message' => 'El usuario se ha creado',
                    'user' => $user
                );
            }
      }else{
        $data = array(
            'status' => 'error',
            'code'   => 404,
            'message' => 'Los datos enviados no son correctos'
            
        );
      }
     

       return response()->json($data, $data['code']);
    }

    public function login(Request $request){
        $jwtAuth = new \JwtAuth();

        // Recibier datos por POST
        $json =$request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        // Validar esos datos
        $validate = \Validator::make($params_array, [
            'email'      => 'required|email',  
            'password'   => 'required'
    ]);

        if($validate->fails()){
                // la validacion ha fallado
                $signup = array(
                    'status' => 'error',
                    'code'   => 404,
                    'message' => 'El usuario no se ha podido identificar',
                    'errors' =>  $validate->errors()
                );
        
        }else{
            // Cifrar la password
            $pwd = hash('sha256', $params->password);
            // Devolver token o datos
            $signup = $jwtAuth->signup($params->email, $pwd);
            
            // En caso de pasarle el parametro getToken
            if(!empty($params->getToken)){
                $signup = $jwtAuth->signup($params->email, $pwd, true);
            }
        }


       

        return response()->json($signup, 200);
    }

    public function update(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checktoken($token);

        if($checkToken){
            echo "<h1>Login correcto</h1>";
        }else{
            echo "<h1>Login incorrecto</h1>";
        }
        die(); // corto la ejecucion del programa para que no pida ninguna vista, ni ninguna response como tal.
    }
}
