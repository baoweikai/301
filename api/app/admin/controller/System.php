<?php
namespace app\admin\controller;

use app\common\model\System as Model;

/**
 * 系统设置
 */
class System extends \core\Controller
{
    protected $middleware = ['auth'];
    protected $model = '\app\common\model\System'; // 对应表格
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
}