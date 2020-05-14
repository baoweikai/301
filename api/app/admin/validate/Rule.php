<?php
namespace app\admin\validate;

use think\facade\Validate;


class Rule extends Validate{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'title' => 'require|max:15',
        'status' => 'require',
        'href' =>'require'
    ];

    /**
     * 错误提示
     * @var array
     */
    protected $message = [
        'role_name.require' => '请填写节点',
        'username.max'     => '节点名最多不能超过15个字符',
        'status.require' => '请选择状态',
        'require.require'  => '请填写控制器/方法',
    ];
}


