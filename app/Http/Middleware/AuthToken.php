<?php

namespace App\Http\Middleware;

use Closure;
use Dingo\Api\Routing\Helpers;
use GuzzleHttp\Client;

class AuthToken
{
    use Helpers;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try{
//            $client =new Client();
//            $response=$client->request('GET','http://39.100.40.121:8080/api/user',[
//                'headers'=>[
//                    'Authorization'=>'Bearer '.$request->input('token')
//                ]
//            ]);
//
//            $body = $response->getBody();
//            $rest_json=json_decode($body->getContents(),true);
//            if(!$rest_json||isset($rest_json['code'])&&$rest_json['code']==700){
//                throw  new \Exception();
//            }
            $request->attributes->add(['user'=>['userId'=>'1124151110244003841','name'=>'xiaoy']]);
//            $request->attributes->add(['user'=>$rest_json]);
            return $next($request);
        }catch (\Exception $e){
            return $this->response->error('用户token验证失败',401);
        }

    }
}
