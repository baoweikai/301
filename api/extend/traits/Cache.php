<?php
namespace traits;

use think\Db;
/*
 * CCategoryBehavior class file.
 *
 * @author Weikai
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

trait Cache
{
    protected static $_rowList;
    /*
     * 缓存名
     */
    private static function getCacheName(){
        return self::getTable() . 'List';
    }

    // 获取所有行的记录并将其写入缓存
    public static function getRowList($attributes = []){

        if(self::$_rowList !== null){
            return self::$_rowList;
        }

        self::$_rowList = cache(self::getCacheName());
        // var_dump(self::$_rowList);die();
        if(self::$_rowList === null)
        {
            self::setRowList($attributes);
        }
        return self::$_rowList;
    }

    /*
     * 更新所有记录列表缓存
     */
    public static function setRowList($attributes = []){
        $rowList = self::where([])->order('serial ASC')->field($attributes)->select();
        foreach($rowList as $row){
            self::$_rowList[$row['id']] = $row;
        }
        cache(self::getCacheName(), self::$_rowList, 3600 * 24 * 7);
    }

    // 获取每一行
    public static function getRowById($id){
        self::$_rowList = self::getRowList();
        if(is_array(self::$_rowList) && isset(self::$_rowList[$id])){
            return self::$_rowList[$id];
        }else{
            return null;
        }
    }

    // 通过id获取改行记录的名称
    public static function getAttributeByPk($id, $attr='name'){
        self::$_rowList = self::getRowList();
        if(is_array(self::$_rowList) && isset(self::$_rowList[$id]) && isset(self::$_rowList[$id][$attr])){
            return self::$_rowList[$id][$attr];
        }else{
            return null;
        }
    }
	/*
	 * 根据主键获取属性值
	 */
	public static function listData($attr='name', $attributes = []){
		self::$_rowList = self::getRowList();
		$listData = [];
		foreach(self::$_rowList as $row){
			$listData[$row['id']] = isset($row[$attr])?$row[$attr]:'';
		}
		return $listData;
	}
}