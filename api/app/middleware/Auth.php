<?php

namespace app\middleware;

use thans\jwt\facade\JWTAuth;
use thans\jwt\exception\JWTException;

class Auth
{
    public function handle($request, \Closure $next)
    {
        // token验证
        try {
            JWTAuth::auth();
            $payload = JWTAuth::getPayload();
            $request->uid = $payload['id']->getValue('id'); 
        } catch (JWTException $e) {
            if(request()->isPjax()){
                return json(['state' => 401, 'message' => $e->getMessage()], 401);
            } else {
                return redirect('/identity/login');
            }
        }

        return $next($request);
    }
}