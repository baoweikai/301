<?php

namespace core;
use think\facade\Validate;

/*
 * 基本模型,所有的模型都继承于它
 */
class Model extends \think\Model
{
    protected $pk = 'id';                // 主键
    protected $autoWriteTimestamp = false;    // 自动时间戳
    protected $createTime = 'create_at';   // 创建时间
    protected $updateTime = 'update_at';   // 更新时间
    protected $dateFormat = 'm-d H:i';

    protected $filter = [];                 // 搜索字段
    protected $order = [];                  // 排序
    protected $rule = [];                // 验证规则

    public $error;

    // 定义全局的查询范围
    protected function base($query)
    {
        $query->order($this->order);
    }
    /*
     * 输入数据验证
     * @param array $params
     * @return bool
     */
    public function validate($params){
        $rules = $this->isExists() ? array_intersect_key($this->rule, $params) : $this->rule;
        $validate = Validate::rule($rules);

        if(!$validate->check($params)){
            $this->error = $validate->getError();
            return false;
        }
        return true;
    }

    /*
     * 表单数据处理
     * @param string $node
     * @return bool
     */
    public function _form($data, $isAfter = false){
        if(!$this->validate($data)){
            return false;
        }

        if($this->allowField($this->fillable)->save($data)){
            $isAfter && $this->afterSave($data);
            return $this->toArray();
        }else{
            return false;
        }
    }
    /*
     * 保存后续处理
     */
    protected function afterSave($data) {}
    /*
     * 列表
     * @param string $node
     * @return bool
     */
    public function _list($params = [], $isPage = true, $with = []){
        if ($isPage) {
            $size = isset($params['pageSize']) ? $params['pageSize'] : 10;
            // 使用查询构造器查询
            $result = self::with($with)->withAttr('cover')->withSearch($this->filter, $params)->order($this->order)->paginate($size);

            $return = [
                'total' => $result->total(),
                'list' => $result->items()
            ];
        }else{
            $result = self::with($with)->withSearch($this->filter, $params)->order($this->order)->select()->toArray();
            $return = ['total' => count($result), 'list' => $result];
        }
        return $return;
    }
    /*
     * 批量保存
     */
    public function batch($data, bool $replace = true){
        $rows = [];
        foreach($data as $row){
            if($this->validate($row)){
                $rows[] = $row;
            } else {
                $this->error = $this->error;
                return false;
            }
        }

        return $this->allowField($this->fillable)->saveAll($rows, $replace);
    }
    /*
     * 选择列表
     * @param string $node
     * @return bool
     */
    public function _select($params = [], $attr = ''){
        // 使用查询构造器查询
        $result = self::field(['id', $attr => 'name'])->withSearch($this->filter, $params)->order($this->order)->paginate(10);
        return ['total' => $result->total(), 'list' => $result->items()];
    }
    /*
     * 排序
     * @param string $node
     * @return bool
     */
    public function _sort($params){
        foreach ($params as $key => $value) {
            if (false === self::where(['id' => $key])->update(['serial' => $value])) {
                return false;
            }
        }
        return true;
    }
    /*
     * 可否删除
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function ableDel()
    {
        return true;
    }
}
