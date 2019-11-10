<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller
{
   
    public function __construct(){
        $this->middleware('api.auth', ['except'=>['index', 'show']]);
    }

    public function index(){
        $posts = Post::all()->load('category');

        return  response()->json([
            'code' => 200,
            'status' => 'success',
            'posts' => $posts
        ],200);
    }

    public function show($id){
        $post = Post::find($id)->load('category');

        if(is_object($post)){

            $data = [
            'code' => 200,
            'status' => 'success',
            'post' => $post
            ];

        }else{
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El post no existe.'
                ];
        }

        return response()->json($data, $data['code']);
    }

    public function store(Request $request){
        // Recoger datos por post
        $json = $request->input('json', null);
        $params  = json_decode($json);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
        // Conseguir al usuario identificado
            $jwtAuth = new JwtAuth();
            $token = $request->header('Authorization', null);
            $user = $jwtAuth->checkToken($token, true);

        // validar los datos
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',
                'image' => 'required'
            ]);
            
            if($validate->fails()){
                $data = [
                    'code' => '400',
                    'status'  => 'error',
                    'message' => 'No se ha guardado el post, faltan datos.'
                ];
            }else{
                // Guardar el post
                $post = new Post();
                $post->user_id = $user->sub;
                $post->category_id = $params->category_id;
                $post->title = $params->title;
                $post->content = $params->content;
                $post->image = $params->image;
                $post-> save();

                $data = [
                    'code' => '200',
                    'status'  => 'success',
                    'post' => $post
                ];
            }

        
        }else{
            $data = [
                'code' => '400',
                'status'  => 'error',
                'message' => 'Los datos recibidos no son validos.'
            ];
        }
        // Devolver la respuesta

        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request){
        // Recoger los datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        // Datos para devolver
        $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => 'Datos enviados incorrectamente.'
        );

        if(!empty($params_array)){
            // Validar datos
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required'
            ]);

            if($validate->fails()){
                $data['errors'] = $validate->errors();
                return response()->json($data, $data['code']);
            }

            // Eliminar lo que no queremos actualizar
            unset($params_array['id']);
            unset($params_array['created_at']);
            unset($params_array['user']);
            unset($params_array['user_id']);

            // Actualizar el post
            $post = Post::where('id', $id)->updateOrCreate($params_array);

            // Devolver
            $data = array(
                'code' => 200,
                'status' => 'success',
                'post' => $post,
                'changes' => $params_array
            );
        }

        return response()->json($data, $data['code']);
    }

    public function destroy($id , Request $request){
        //Conseguir el registro(post)
        $post = Post::find($id);

        if(!empty($post)){
            // Borrarlo
            $post->delete();

            // Devolver algo
            $data=[
                'code' => 200,
                'status' => 'success',
                'post' => $post
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'El post no existe.'
            ];
        }
        
        return response()->json($data, $data['code']);

    }
}
