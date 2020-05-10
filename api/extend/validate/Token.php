<?php
namespace validate;

use core\Validate;
/*
 * 生成token参数验证器
 */
class Token extends Validate
{

	protected $rule = [
        'appid'       =>  'require',
        'mobile'      =>  'mobile|require',
        'nonce'       =>  'require',
        'timestamp'   =>  'number|require',
        'sign'        =>  'require'
    ];

    protected $message  =   [
        'appid.require'    => 'appid不能为空',
        'mobile.mobile'    => '手机格式错误',
        'nonce.require'    => '随机数不能为空',
        'timestamp.number' => '时间戳格式错误',
        'sign.require'     => '签名不能为空',
    ];
    // 校验时间戳
    public function check () {
        //时间戳校验
        if(abs($params['timestamp'] - time()) > self::$timeDif){
            return self::returnMsg(401,'请求时间戳与服务器时间戳异常','timestamp：'.time());
        }

        //appid检测，这里是在本地进行测试，正式的应该是查找数据库或者redis进行验证
        if($params['appid'] !== self::$appid){
            return self::returnMsg(401,'appid 错误');
        }
        // 校验签名
        $sign = Oauth::makeSign($params,self::$appsercet);
        if($sign !== $params['sign']){
            return self::returnMsg(401,'sign错误','sign：'.$sign);
        }
    }

}
