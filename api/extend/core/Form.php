<?php

namespace core;

/*
 * 后台权限基础控制器
 * Class Form
 */
class Form
{
    // 自定义属性
    protected $filter = [];              // 搜索过滤
    protected $rule = [];                // 验证规则

    /*
     * 输入数据验证
     * @param string $node
     * @return bool
     */
    public function validate($params){
        // 验证
        $validate = new \think\Validate;
        if(!$validate->rule($this->rule)->check($params)){
            $this->error = $validate->getError();
            return false;
        }
        return true;

    }
}
