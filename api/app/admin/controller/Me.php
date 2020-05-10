<?php

namespace app\admin\controller;

use core\Controller;
use app\admin\model\Admin as Model;
use thans\jwt\facade\JWTAuth;

/*
 * 系统用户管理控制器
 */

class Me extends Controller
{
    protected $middleware = ['auth'];  // 中间件
    protected $model = '\app\model\Admin';
    protected $name = '管理员';

    /*
     * 用户密码修改
     * @return array|string
     */
    public function pass()
    {
        $model = $this->loadModel(request()->uid);
        if (request()->isGet()) {
            return $this->success(['vo' => $model]);
        }
        $post = input('post.');
        if ($post['password'] !== $post['repeat']) {
            return $this->error('两次输入的密码不一致！', 201);
        }
        $data = ['password' => $post['password']];
        if ($model->save($data)) {
            return $this->success([], '密码修改成功，下次请使用新密码登录！');
        }
        return $this->error('密码修改失败，请稍候再试！', 201);
    }

    /*
     * 编辑
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\Exception
     */
    public function edit($id)
    {
        $model = $this->loadModel(request()->uid);

        $this->title = '编辑' . $this->name;
        if (!request()->isPost()) {
            $this->beforeForm($model);
            return $this->success(['vo' => $model], 'form');
        }

        $params = input('post.', []);

        if (!empty($params) && $model->_form($params)) {
            return $this->success([], '恭喜, 数据保存成功!');
        }
        $error = $model->getError();
        return $this->error(empty($error) ? '未作修改' : $error, 201);
    }
    /*
     * 个人信息
     */
    public function info()
    {
        $admin = Model::with('role')->where('id', request()->uid)->find();
        return $this->success($this->result + [
            'nickname' => $admin->nickname,
            // 'avatar' => $admin->avatar,
            'perms' => ['home']
        ]);
    }
}
