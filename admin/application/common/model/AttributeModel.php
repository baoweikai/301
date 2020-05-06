<?php
namespace app\common\model;
class  AttributeModel extends BaseModel
{
    protected $resultSetType = 'collection';
    protected $autoWriteTimestamp = true;
    //定义属性
    protected $type = [
        'status' => 'integer',
        'create_time' => 'timestamp:Y-m-d H:i:s',
        'update_time' => 'timestamp:Y-m-d H:i:s',
    ];

    protected function getStatusAttr($value, $data)
    {
        $status_arr = [0 => '关闭', 1 => '开启'];
        return isset($data['return_arr']) && $data['return_arr'] ? $status_arr : ['val' => $value, 'text' => $status_arr[$value]];
    }

    /**
     * 获取属性列表
     */
    public function getAttrList()
    {
        $map["status"] = 1;
        $field = "id,name";
        $list = $this->where($map)->field($field)->select();
        return $list;
    }
}
?>