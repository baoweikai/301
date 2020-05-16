<?php

namespace core;

/*
 *  控制器基类
 */
abstract class Controller
{
    protected $isPage = true;  // 列表页是否分页
    protected $isAfter = false;  // 保存完毕是否后续
    protected $with = []; // 联表查询
    protected $extend = []; // 表单扩展项,通过获取器获取
    protected $view = []; // 详情展示项
    protected $model = ''; // 对应模块
    protected $defaults = []; // 默认值
    protected $filter = [];  // 搜索条件
    protected $result = [    // 返回结果
        'columns' => []   // 储存在store当中,便于各组件调用
    ];
    /*
     * 空操作
     */
    public function _empty($name)
    {
        return $this->error('当前页面不存在', 404);
    }
    /*
     * 列表
     */
    protected function _index($params = [])
    {
        $model = new $this->model;
        $this->result += $model->_list(input('get.', []) + $params, $this->isPage, $this->with);

        $this->beforeIndex();

        return $this->success($this->result);
    }
    /*
     * 选择列表
     */
    protected function _select($attr = 'name', $params = [])
    {
        $model = new $this->model;
        $this->result += $model->_select(input('get.', []) + $params, $attr);
        return $this->success($this->result);
    }
    /*
     * 导出数据
     */
    protected  function _export($params = [])
    {
        $this->isPage = false;
        $params = $params + input('get.', []);
        $model = new $this->model;
        $result = $model::field($this->export)->with($this->with)->withSearch($this->filter, $params)->select()->toArray();
        \helper\Excel::exportExcel($this->filter,$result);
    }
    /*
     * 列表数据预处理
     */
    protected function beforeIndex()
    {
    }
	/*
	 * 数据更新后处理
	 */
	protected function afterSava()
	{
	}
    /*
     * 添加
     */
    protected function _add()
    {
        $model = new $this->model($this->defaults);
        $this->result['view'] = $model->toArray();
        $this->beforeForm($model);
        $this->result['meta']['title'] = '添加';
        return $this->success($this->result);
    }
    /*
     * 保存
     */
    protected function _save()
    {
        $model = new $this->model;
        $post = input('post.', []);

        if($model->_form($this->defaults + $post, $this->isAfter)){
            $this->result += $model->toArray();
            return $this->success($this->result);
        }

        return $this->error($model->error, 201);
    }
    /*
     * 编辑
     */
    protected function _edit($id)
    {
        $this->result['meta']['title'] = '编辑';
        $model = $this->loadModel($id);
        $this->result['view'] = $model->toArray();
        foreach($this->view as $v){
            $this->result['view'][$v] = $model->{$v};
        }
        $this->beforeForm($model);

        return $this->success($this->result);
    }
    /*
     * 更新
     */
    protected function _update($id)
    {
        $model = $this->loadModel($id);
        $post = input('post.', []);
        if(empty($post)){
            $this->error('数据不能为空', 201);
        }
        $post['id'] = $id; // 更新时可以忽略自身unique规则

        if($model->_form($post, $this->isAfter) || empty($model->error)){
            $this->result += $model->toArray();
            return $this->success($this->result);
        }
        return $this->error($model->error, 201);
    }
    /*
     * 编辑前数据处理
     */
    protected function beforeForm($model = null){
    }
    /*
     * 模块信息
     */
    protected function _view($where)
    {
        $model = $this->loadModel($where);
        $this->result['view'] = $model->toArray();
        foreach($this->view as $key){
            $this->result['view'][$key] = $model->{$key};
        }
        $this->beforeForm($model);

        return $this->success($this->result);
    }
    /*
     * 删除
     */
    public function del($id)
    {
        return $this->_drop($id);
    }
    /*
     * 删除
     */
    protected function _drop($where)
    {
        $model = $this->loadModel($where);
        if(!$model->ableDel()){
            return $this->error($model->error, 401);
        }
        // $model->status = 0;
        if ($model->delete()) {
            return $this->success();
        }
        
        return $this->error("删除失败，请稍候再试！", 401);
    }

    protected  function _switch(){
        $ids = input('post.ids', []);
        $attr = input('post.attr', 'status');
        $value = input('post.value', 0);
        if($this->model::where('id', 'in', $ids)->update([$attr => $value])){
            $this->updateIds = $ids;
            $this->model::afterWrite($this);
            return $this->success();
        }
        return $this->error("修改失败！");
    }
    /*
     * 排序操作
     */
    protected function _resort(){
        // 列表排序默认处理
        if ($this->request->isPost() && input('post.action') === 'resort') {
            $model = new $this->model;
            if(!$model->_sort(input('post.'))){
                return $this->error('列表排序失败, 请稍候再试', 401);
            }
            return $this->success([], '列表排序成功, 正在刷新列表');
        }
    }
    // 审核通过
    protected function _adopt($ids = [])
    {
        if($this->model::where('id', 'in', $ids)->update(['exame_status' => 2])){
            return $this->success($this->result);
        }
        return $this->error('', 201);
    }
    // 审核驳回
    protected function _reject($ids = [])
    {
        if($this->model::where('id', 'in', $ids)->update(['exame_status' => 3])){
            return $this->success($this->result);
        }
        return $this->error('', 201);
    }
    // 加载model
    protected function loadModel($params, $exce = true){
        $where = is_string($params) || is_numeric($params) ? [['id', '=', $params]] : $params;
        $model = $this->model::where($where)->with($this->extend)->find();

        if($model === null){
            if($exce){
                throw new \think\Exception('当前模块不存在', 244);
            } else {
                return false;
            }
        }
        return $model;
    }
    // json
    protected function error($msg, $code = 201, $result = []){
        $ret = [
            'state' => $code,
            'message'  => $msg,
            'result' => $result,
        ];

        return json($ret, $code);
    }
    // json
    protected function success($result = [], $msg = ''){
        $ret = [
            'state' => 200,
            'message'  => $msg,
            'result' => $result,
        ];

        return json($ret, 200);
    }
}
