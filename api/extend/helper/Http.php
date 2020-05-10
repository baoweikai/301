<?php

namespace helper;

use think\exception\HttpResponseException;

/*
 * HTTP请求服务
 */
class Http
{
    public static $ch;

    /*
     * 以get模拟网络请求
     * @param string $url HTTP请求URL地址
     * @param array $query GET请求参数
     * @param array $options CURL参数
     * @return bool|string
     */
    public static function get($url, $query = [], $options = [])
    {
        $options['query'] = $query;
        return json_decode(self::request('get', $url, $options), true);
    }
    /*
     * 以post模拟网络请求
     * @param string $url HTTP请求URL地址
     * @param array $data POST请求数据
     * @param array $options CURL参数
     * @return bool|string
     */
    public static function post($url, $data = [], $options = [])
    {
        $options['data'] = $data;
        $options['header'] = ['Content-Type' => 'multipart/form-data'];
        return json_decode(self::request('post', $url, $options), true);
    }

    /*
     * CURL模拟网络请求
     * @param string $method 请求方法
     * @param string $url 请求方法
     * @param array $options 请求参数[header,data,ssl_cer,ssl_key]
     * @return bool|string
     */
    public static function request($method, $url, $options = [])
    {
        if(!self::$ch){
            self::$ch = curl_init();
            // 请求超时设置
            $options['timeout'] = isset($options['timeout']) ? $options['timeout'] : 60;
            curl_setopt(self::$ch, CURLOPT_TIMEOUT, $options['timeout']);
            // CURL头信息设置
            $header = [];
            if (!empty($options['header'])) {
                $header = array_merge($header, $options['header']);
            }
            curl_setopt(self::$ch, CURLOPT_HTTPHEADER, $header);
            // 证书文件设置
            if (!empty($options['ssl_cer']) && file_exists($options['ssl_cer'])) {
                curl_setopt(self::$ch, CURLOPT_SSLCERTTYPE, 'PEM');
                curl_setopt(self::$ch, CURLOPT_SSLCERT, $options['ssl_cer']);
            }
            if (!empty($options['ssl_key']) && file_exists($options['ssl_key'])) {
                curl_setopt(self::$ch, CURLOPT_SSLKEYTYPE, 'PEM');
                curl_setopt(self::$ch, CURLOPT_SSLKEY, $options['ssl_key']);
            }
            curl_setopt(self::$ch, CURLOPT_POST, strtolower($method) === 'post');
        }
        // GET参数设置
        if (!empty($options['query'])) {
            $url .= stripos($url, '?') !== false ? '&' : '?' . http_build_query($options['query']);
        }
        // POST数据设置
        if (strtolower($method) === 'post') {
            curl_setopt(self::$ch, CURLOPT_POSTFIELDS, self::build($options['data']));
        }

        curl_setopt(self::$ch, CURLOPT_URL, $url);
        curl_setopt(self::$ch, CURLOPT_HEADER, false);
        curl_setopt(self::$ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt(self::$ch, CURLOPT_SSL_VERIFYHOST, false);
        // curl_setopt(self::$ch, CURLOPT_SSLVERSION, 3);
        curl_setopt(self::$ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(self::$ch, CURLOPT_TIMEOUT, 4000);
        curl_setopt(self::$ch, CURLOPT_FOLLOWLOCATION, true);
        
        list($ret, $status, $error) = [curl_exec(self::$ch), curl_getinfo(self::$ch), curl_error(self::$ch)];
        
        if(intval($status["http_code"]) !== 200){
            throw new \think\exception\HttpException($status["http_code"], $error);
        }
        return $ret;
    }

    /*
     * POST数据过滤处理
     * @param array $data
     * @param bool $needBuild
     * @return array
     */
    private static function build($data, $needBuild = true)
    {
        if (!is_array($data)) {
            return $data;
        }
        foreach ($data as $key => $value) {
            if (is_string($value) && class_exists('CURLFile', false) && stripos($value, '@') === 0) {
                if (($filename = realpath(trim($value, '@'))) && file_exists($filename)) {
                    list($needBuild, $data[$key]) = [false, new \CURLFile($filename)];
                }
            }
        }
        return $needBuild ? http_build_query($data) : $data;
    }

}
