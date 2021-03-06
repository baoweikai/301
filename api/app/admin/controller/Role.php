<?php
namespace app\admin\controller;

class Role extends \core\Controller
{
    protected $middleware = ['auth'];
    protected $model = '\app\admin\model\Role'; // 对应表格
    protected $name = '管理员';

    public function index()
    {
        return $this->_index();
    }
    // 添加
    public function add()
    {
        return $this->_add();
    }
    // 保存
    public function save()
    {
        return $this->_save();
    }
    // 编辑
    public function edit($id)
    {
        return $this->_edit($id);
    }
    // 编辑
    public function update($id)
    {
        return $this->_update($id);
    }
    // 切换
    public function switch()
    {
        return $this->_switch();
    }
}