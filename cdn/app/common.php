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
