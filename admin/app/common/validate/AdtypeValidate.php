<?php
namespace app\common\validate;

use think\facade\Validate;


class AdtypeValidate extends  Validate{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'typename' => 'require|unique:Adtype',
        'tag' => 'require|unique:Adtype',
        'sort' => 'require|number',
        'status' => 'require|in:0,1',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected $message = [
        'typename.require' => '请填写广告位名称',
        'typename.unique' => '广告位名称重复',
        'tag.require' => '请填写广告位标识',
        'tag.unique' => '广告位标识重复',
        'sort.require' => '请填写排序',
        'sort.number' => '排序必须为数子',
        'status.require' => '请选择状态',
        'status.in' => '状态只能为0或1',
    ];
}


