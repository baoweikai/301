<?php
namespace helper;

/*
 * 层级结构
 *
 * @author Weikai
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

class Hierarchy
{
    static $rows = [];
    static $props = ['label' => 'name', 'value' => 'id', 'pid' => 'pid'];  // 属性对应数组
    static $level = 0;
    /*
     * 树形表格数据
     */
    public static function init($rows, $props = [])
    {
        self::$rows = $rows;
        self::$props = $props + self::$props;
    }
        /*
         * 树形表格数据
         */
    public static function _table($pid = 0){
        $data = [];
        foreach(self::$rows as $i => $row){
            if($row[self::$props['pid']] != $pid) { continue; } // 找不到子菜单, 跳出当次循环
            $children = self::_table($row[self::$props['value']]);
            $data[] = count($children) > 0 ? $row + ['children' => $children] : $row;
            unset(self::$rows[$i]);
        }
        return $data;
    }
    /*
     * 树形结构
     */
    public static function _tree($pid = 0){
        $data = [];
        foreach(self::$rows as $i => $row){
            if($row[self::$props['pid']] != $pid) { continue; } // 找不到子菜单, 跳出当次循环
            $arr = [];
            foreach(self::$props as $key => $prop){
                $arr[$key] = $row[$prop];
            }
            $children = self::_tree($row[self::$props['value']]);
            $data[] = count($children) > 0 ? $arr + ['children' => $children] : $arr;
            unset(self::$rows[$i]);
        }
        return $data;
    }
    /*
     * 获取数据树子ID
     */
    public static function _ids($pid = 0)
    {
        $data = [];
        foreach (self::$rows as $k => $row) {
            if($row[self::$props[self::$props['value']]] != $pid) { continue; } // 找不到子菜单, 跳出当次循环
            $data = array_merge([$row[self::$props['value']]], self::_ids($row[self::$props['value']]));
            unset(self::$rows[$k]);
        }
        return $data;
    }
    /*
     * 获取数据树顶级ID
     * @param int $id 起始ID
     * @return array
     */
    public static function _pid($list, $id = 0)
    {
        $data = [$id];
        if ($list[$id][self::$props['pid']] != 0) {
            $data  = array_merge(self::_pid($list, $list[$id][self::$props['pid']]), $data);
        }
        return $data;
    }
}
