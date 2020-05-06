<?php
namespace app\common\model;
/**
 * 广告分类模型
 */
class  AdtypeModel extends BaseModel
{
    protected $resultSetType = 'collection';
    protected $auto = ['status' ,'sort'];
    protected $autoWriteTimestamp = true;
    //定义属性
    protected $type = [
        'sort' => 'integer',
        'status' => 'integer',
        'create_time' => 'timestamp:Y-m-d H:i:s',
        'update_time' => 'timestamp:Y-m-d H:i:s',
    ];

    protected function setSortAttr($value)
    {
        return $value ? $value : 50;
    }

    protected function getStatusAttr($value, $data)
    {
        $status_arr = [0 => '关闭', 1 => '开启'];
        return isset($data['return_arr']) && $data['return_arr'] ? $status_arr : ['val' => $value, 'text' => $status_arr[$value]];
    }
    /**
     * 删除广告分类
     * @param  int    $typeId 分类ID
     * @return 
     */
    public function  delAdtype($typeId)
    {
        $typeId  = intval($typeId);
        if(model('Ad')->where(['typeid' => $typeId])->count()>0){
            $this->error = '请先删除广告';
            return false;
        }
        $map['id'] = $typeId;
        $result = $this->where($map)->delete();
        if(!$result) {
            $this->error = '删除失败';
            return false;
        }
        $this->error = '删除成功';
        return true;
    }


    public  function adTypeAll()
    {
        $map['status'] = 1;
        $result = $this->field('id,typename')->all($map);
        return $result; 
    }
}
?>