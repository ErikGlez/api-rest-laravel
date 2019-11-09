<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checktoken($token);

        if($checkToken){
            return $next($request);
        }else{
            $data = array(
                'status' => 'error',
                'code'   => 404,
                'message' => 'El usuario no esta identificado',
                
            );

            return response()->json($data, $data['code']);
        }

        
    }
}
