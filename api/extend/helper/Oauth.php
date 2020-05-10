<?php

namespace helper;

use think\facade\Request;
use think\facade\Cache;

/*
 * 生成token
 */
class Oauth
{
    /*
     * 请求时间差
     */
    public static $timeDif = 10000;

    public static $accessTokenPrefix = 'accessToken_';
    public static $refreshAccessTokenPrefix = 'refreshAccessToken_';
    public static $expires = 7200;
    public static $refreshExpires = 60 * 60 * 24 * 30;   //刷新token过期时间
    /*
     * 测试appid，正式请数据库进行相关验证
     */
    public static $appid = 'ant';
    /*
     * appsercet
     */
    public static $appsercet = '!@kiiu808f03idku399877vakxo1o0';

    /*
     * 认证授权 通过用户信息和路由
     * @param Request $request
     * @return \Exception|UnauthorizedException|mixed|Exception
     * @throws UnauthorizedException
     */
    public static function auth()
    {
        $data = self::client();

        $getCacheAccessToken = Cache::get(self::$accessTokenPrefix . $data['access_token']);  //获取缓存access_token
        if (!$getCacheAccessToken) {
            exception('Invalid access_token', 401);
        }
        if ($getCacheAccessToken['client']['appid'] !== $data['appid']) {
            exception('appid错误', 401);
        }
        return $data;
    }
    /*
     * 获取用户信息
     * @param Request $request
     * @return $this
     * @throws UnauthorizedException
     */
    public static function client()
    {
        //获取头部信息
        try {
            $authorization = Request::header('authorization');   //获取请求中的authentication字段，值形式为USERID asdsajh..这种形式
            if (empty($authorization)) {
                exception('Invalid authorization credentials', 401);
            }
            list($appid, $access_token, $user_id) = explode(',', base64_decode($authorization));  //对base_64解密，获取到用:拼接的自字符串，然后分割，可获取appid、accesstoken、uid这三个参数
            if (empty($appid)) {
                exception('Invalid appid', 401);
            }

            if (empty($access_token)) {
                exception('Invalid access_token', 401);
            }
            if (empty($user_id)) {
                exception('Invalid user_id', 401);
            }
            return ['appid' => $appid, 'access_token' => $access_token, 'user_id' => $user_id];
        } catch (Exception $e) {
            exception('Invalid authorization credentials', 401);
            // $this->json('Invalid authorization credentials', 401, $e);
        }
    }

    /*
     * 检测当前控制器和方法是否匹配传递的数组
     * @param array $arr 需要验证权限的数组
     * @return boolean
     */
    public static function match($arr = [])
    {
        $request = Request::instance();
        $arr = is_array($arr) ? $arr : explode(',', $arr);
        if (!$arr) {
            return false;
        }
        $arr = array_map('strtolower', $arr);
        // 是否存在
        if (in_array(strtolower($request->action()), $arr) || in_array('*', $arr)) {
            return true;
        }

        // 没找到匹配
        return false;
    }

    /*
     * 生成签名
     * _字符开头的变量不参与签名
     */
    public static function sign($data = [], $app_secret = '')
    {
        unset($data['version']);
        unset($data['sign']);
        ksort($params);
        $params['key'] = $app_secret;
        return strtolower(md5(urldecode(http_build_query($params))));
    }

    /*
     * 刷新token
     */
    public function token($appid = '')
    {
        // 重新给用户生成调用token
        $data = ['appid' => $appid];
        $accessToken = Token::setAccessToken($data);
        return $accessToken;
    }
}


/*
 * 生成token
 */
class Token
{
    /*
     * 请求时间差
     */
    public static $timeDif = 10000;
    public static $accessTokenPrefix = 'accessToken_';
    public static $expires = 7200;
    public static $refreshExpires = 60 * 60 * 24 * 30;   //刷新token过期时间
    /*
     * 测试appid，正式请数据库进行相关验证
     */
    public static $appid = 'ant';
    /*
     * appsercet
     */
    public static $appsercet = '!@kiiu808f03idku399877vakxo1o0';

    /*
     *  获取token
     */
    public function get($userInfo = [])
    {
        //参数验证
        try {
            $accessToken = self::set(array_merge($userInfo, input('')));  //传入参数应该是根据手机号查询改用户的数据
            return $accessToken;
        } catch (Exception $e) {
            exception('获取token失败', 500);
        }
    }

    /*
     * 设置AccessToken
     * @param $clientInfo
     * @return int
     */
    public function set($clientInfo)
    {
        //生成令牌
        $accessToken = self::build();

        $now = time();
        $accessTokenInfo = [
            'access_token' => $accessToken,//访问令牌
            'expires_time' => $now + self::$expires,      //过期时间的时间戳
            'client' => $clientInfo,//用户信息
        ];
        //存储accessToken
        cache(self::$accessTokenPrefix . $accessToken, $accessTokenInfo, self::$expires);
        return $accessTokenInfo;
    }

    /*
     * 生成AccessToken
     * @return string
     */
    private static function build($length = 32)
    {
        //生成AccessToken
        $str_pol = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789abcdefghijklmnopqrstuvwxyz";
        return substr(str_shuffle($str_pol), 0, $length);
    }

    /*
     * 生成签名
     * _字符开头的变量不参与签名
     */
    private static function sign($data = [], $app_secret = '')
    {
        unset($data['version']);
        unset($data['sign']);
        ksort($params);
        $params['key'] = $app_secret;
        return strtolower(md5(urldecode(http_build_query($params))));
    }
}
