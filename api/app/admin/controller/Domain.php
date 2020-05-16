<?php
namespace app\admin\controller;

use app\common\model\Group;
use app\common\model\Domain as Model;

class Domain extends \core\Controller
{
    protected $middleware = ['auth'];
    protected $model = '\app\common\model\Domain'; // 对应表格
    protected $with = ['user', 'group'];
    protected $extend = ['user'];
    protected $name = '管理员';

    protected function beforeForm($model = null){
        $this->columns($model);
    }
    private function columns($model = null){
        // 各个字段编辑时需要的数据
        $this->result['columns'] += [
            'group_id' => ['options' => Group::column('name', 'id')],
            'user_id' => ['text' => isset($model->account) ? $model->account : '']
        ];
    }
    protected function beforeIndex(){
        $this->columns();
    }
    public function index()
    {
        return $this->_index();
    }
    public function select()
    {
        return $this->_select('shield_host');
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