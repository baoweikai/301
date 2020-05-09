<?php
/**
 * 友情链接
 */
namespace app\common\validate;

use think\facade\Validate;

class Links extends  Validate{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'title' => 'require|unique:Links',
        'url' => 'require|unique:Links',
        'sort' => 'require|number',
        'status' => 'require|in:0,1',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected $message = [
        'title.require' => '请填写链接名称',
        'title.unique' => '链接名称重复',
        'url.require' => '请填写链接地址',
        'url.unique' => '链接地址重复',
        'sort.require' => '请填写排序',
        'sort.number' => '排序必须为数子',
        'status.require' => '请选择状态',
        'status.in' => '状态只能为0或1',
    ];
}





?>