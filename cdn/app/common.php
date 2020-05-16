<?php
// 应用公共文件

/**
 * 参数后缀验证
 * @param $param  参数
 * @return  bool
 */
function verifyExt($ext ='')
{
    $extArr = ['', 'html', 'htm', 'jsp', 'php', 'asp'];
    return !in_array($ext, $extArr);
}

/*
* 获取用户真实IP地址
*/
function get_ip(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $cip = $_SERVER['HTTP_CLIENT_IP'];
    }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }else if(!empty($_SERVER["REMOTE_ADDR"])){
        $cip = $_SERVER["REMOTE_ADDR"];
    }else{
        $cip = '';
    }
    preg_match("/[\d\.]{7,15}/", $cip, $cips);
    $cip = isset($cips[0]) ? $cips[0] : '0';
    unset($cips);
    return $cip;
}
/*
function getRealIp()
{
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            foreach ($arr as $ip) {
                $ip = trim($ip);

                if ($ip != 'unknown') {
                    $realip = $ip;
                    break;
                }
            }
        } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $realip = $_SERVER['REMOTE_ADDR'];
        } else {
            $realip = '0.0.0.0';
        }
    } else if (getenv('HTTP_X_FORWARDED_FOR')) {
        $realip = getenv('HTTP_X_FORWARDED_FOR');
    } else if (getenv('HTTP_CLIENT_IP')) {
        $realip = getenv('HTTP_CLIENT_IP');
    } else {
        $realip = getenv('REMOTE_ADDR');
    }

    preg_match('/[\\d\\.]{7,15}/', $realip, $onlineip);
    $realip = (!empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0');
    return $realip;
}
*/

function get_client_ip()
{
    foreach (array(
                'HTTP_CLIENT_IP',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_X_FORWARDED',
                'HTTP_X_CLUSTER_CLIENT_IP',
                'HTTP_FORWARDED_FOR',
                'HTTP_FORWARDED',
                'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER)) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                return $ip;
            }
        }
    }
    return null;
}