<?php
// 应用公共文件

/**
 * 参数后缀验证
 * @param $param  参数
 * @return  bool
 */
function verifyExt($param ='')
{
    $ext = substr(strrchr($param,"."), 1);
    $extArr = ["js","css","jpg","jpeg","png","gif","xml","rar","zip","exe","pdf","xls","txt","doc","ico"];
    if(in_array($ext, $extArr)){
        return true;
    }else{
        return false;
    }
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