<?php
namespace app\common\validate;

use think\facade\Validate;

class JumpValidate extends  Validate{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'shield_url' => 'require|url|unique:Jump',
        'jump_url' => 'require|url',

    ];

    /**
     * 错误提示
     * @var array
     */
    protected $message = [
        'jump_url.require' => '请填写跳转URL',
        'jump_url.url' => '跳转URL无效',
        'shield_url.require' => '请填写被墙域名',
        'shield_url.unique' => '被墙域名重复',
        'shield_url.url' => '被墙域名无效',
    ];
}





?>