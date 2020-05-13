<?php
namespace app\admin\controller;

use app\admin\model\Role;

class Admin extends \core\Controller
{
    // protected $middleware = ['auth'];
    protected $model = '\app\admin\model\Admin'; // 对应表格
    protected $name = '管理员';

    protected function beforeForm($model = null){
        $this->columns($model);
    }
    private function columns($model = null){
        // 各个字段编辑时需要的数据
        $this->result['columns'] += [
            'role_id' => ['options' => Role::column('name', 'id')]
        ];
    }
    protected function beforeIndex(){
        $this->columns();
    }
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
    // 密码
    public function pass($id)
    {
        $model = $this->loadModel($id);
        if (request()->isGet()) {
            return $this->success(['vo' => $model]);
        }

        $post = input('post.');
        if ($post['password'] !== $post['confirm']) {
            return $this->error('两次输入的密码不一致！', 201);
        }
        $data = ['password' => $post['password']];
        if ($model->save($data)) {
            return $this->success([], '密码修改成功，下次请使用新密码登录！');
        }
        return $this->error('密码修改失败，请稍候再试！', 201);
    }
    // 切换
    public function switch()
    {
        return $this->_switch();
    }
}