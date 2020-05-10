<?php
namespace traits;

use helper\Hierarchy;

/*
 * 树形结构类
 *
 * @author Weikai
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

trait Tree
{
    use Cache;

    protected static $_tree = null; //  层叠数据

    /*
     * Get menu parent
     */
    public function parent()
    {
        return $this->hasOne(self::getTable(), ['id' => $this->props['pid']]);
    }
    /*
     * 表格数据
     */
    public static function table($pid = 0) {
        Hierarchy::init(self::where('status', 1)->select()->toArray(), self::$props);
        return Hierarchy::_table($pid);
    }
    /*
     * 层叠数据
     */
    public static function getTree($pid = 0, $type = null) {
        if(self::$_tree !== null){
            return self::$_tree;
        }

        self::$_tree = cache(self::getTable() . 'Tree');

        if(self::$_tree !== null){
            return self::$_tree;
        }
        self::setTree($pid, $type);
        return self::$_tree;
    }
    /*
     * 层叠数据
     */
    public static function setTree($pid = 0, $type = null) {
        $where = [['status', '=', 1]];
        if($type !== null){
            $where[] = ['type', '=', $type];
        }
        Hierarchy::init(self::where($where)->select()->toArray(), self::$props);
        self::$_tree = Hierarchy::_tree($pid);
        cache(self::getTable().'Tree' . $pid, self::$_tree, 3600 * 24 * 7);
        return self::$_tree;
    }
    /*
     * 获取数据树子ID
     * @param int $id 起始ID
     * @return array
     */
    public function getSidsAttr($id)
    {
        $list = self::getRowList();
        return Hierarchy::_ids($list);
    }
    /*
     * 获取所有父级ID
     */
    public function getPidsAttr(){
        $list = self::getRowList();
        return Hierarchy::_pid($list, $this->id);
    }
}
