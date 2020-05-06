<?php
namespace app\common\validate;

use think\facade\Validate;


class AttributeValidate extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'name' => 'require|unique:Attribute',
        'status' => 'require|in:0,1',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected $message = [
        'name.require' => '请填写属性名',
        'name.unique' => '属性名称重复',
        'status.require' => '请选择状态',
        'status.in' => '状态只能为0或1',
    ];
}


