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
            return json(['message' => $e->getMessage(), 'state' => 401])->code(401);
        }

        return $next($request);
    }
}