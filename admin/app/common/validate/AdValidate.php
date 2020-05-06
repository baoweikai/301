<?php
namespace app\common\validate;

use think\facade\Validate;


class AdValidate extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'title' => 'require|unique:Ad',
        'typeid' => 'require',
        'url' => 'require',
        'opens'=> 'require|in:0,1',
        'sort' => 'require|number',
        'status' => 'require|in:0,1',
        'litpic' => 'require'
    ];

    /**
     * 错误提示
     * @var array
     */
    protected $message = [
        'title.require' => '请填写广告名称',
        'title.unique' => '广告名称重复',
        'typeid.require' => '请选择广告所属位置',
        'url.require' => '请填写广告链接',
        'sort.require' => '请填写排序',
        'sort.number' => '排序必须为数子',
        'status.require' => '请选择状态',
        'status.in' => '状态只能为0或1',
        'litpic.require' => '请上传图片'
    ];
}


