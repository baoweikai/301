<?php
namespace app\common\validate;

use think\Validate;


class ExpendValidate extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'user_id' => 'gt:0',
        'number' => 'require|between:1,999',
        'desc' => 'require',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected $message = [
        'user_id.gt' => '用户名ID不存在',
        'number.require' => '请填写充值条数',
        'number.between' => '充值范围在1-999之间',
        'desc.require' => '请填写备注',
    ];
}


