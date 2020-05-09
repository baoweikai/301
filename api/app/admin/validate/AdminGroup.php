<?php
namespace app\admin\validate;

use think\facade\Validate;


class AdminGroup extends  Validate{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'title' => 'require|unique:AdminGroup',
        'status' => 'require|in:0,1',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected $message = [
        'title.require' => '请填写角色名',
        'title.unique' =>'角色名重复',
        'status.require' => '请选择状态',
    ];
}


