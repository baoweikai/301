<?php
namespace app\common\validate;

use think\facade\Validate;


class SystemValidate extends  Validate{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'name' => 'require|unique:System',
        'url' => 'require|unique:System',
        'title' => 'require',
        'keywords' => 'require',
        'description' => 'require',
        'address' => 'require',
        'tel' => 'require',
        'email' => 'require',
        'logo' =>'require',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected $message = [
        'name.require' => '请填写网站名称',
        'name.unique' => '网站名称重复',
        'url.require' => '请填写网址',
        'url.unique' => '网址重复',
        'title.require' => '请填写网站标题',
        'keywords.require' => '请填写网站关键字',
        'description.require' => '请填写网站描述',
        'address.require' => '请填写公司地址',
        'tel.require' => '请填写公司电话',
        'email.require' => '请填写公司邮箱',
        'logo.require' => '请上传公司logo',

    ];
}


