<?php
namespace app\common\validate;

use think\Validate;


class CnameValidate extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'cname' => 'require|unique:Cname',
        'attr_id' => 'require|number|gt:0',
        'status' => 'require|in:0,1',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected $message = [
        'cname.require' => '请填写CNAME信息',
        'cname.unique' => 'CNAME信息重复',
        'attr_id.require' => '请选择属性',
        'attr_id.number' => '属性ID必须为数字',
        'attr_id.gt' => '属性ID必须大于0',
        'status.require' => '请选择状态',
        'status.in' => '状态只能为0或1',
    ];
}


