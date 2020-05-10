<?php

namespace helper;

use think\Db;

/*
 * 系统权限节点读取器
 * Class NodeService
 * @package extend
 * @date 2017/05/08 11:28
 */
class Folder
{
     /*
     * 获取节点列表
     * @param string $dirPath 路径
     * @param array $nodes 额外数据
     * @return array
     */
    public static function tree($dirPath, $nodes = [])
    {
        $files = self::scan($dirPath);
        foreach ($files as $filename) {
            $matches = [];
            if (!preg_match('|/\w+/controller/(\w+)|', str_replace(DIRECTORY_SEPARATOR, '/', $filename), $matches) || count($matches) !== 2) {
                continue;
            }
            $className = env('app_namespace') . str_replace('/', '\\', $matches[0]);
            if (!class_exists($className)) {
                continue;
            }
            $index = self::parseNodeStr("{$matches[1]}");
            $nodes[$index] = $index;

            foreach (get_class_methods($className) as $funcName) {
                if (strpos($funcName, '_') !== 0 && $funcName !== 'initialize') {
                    $index = self::parseNodeStr("{$matches[1]}") . '/' . strtolower($funcName);
                    $nodes[$index] = $index;
                }
            }
        }
        return $nodes;
    }

    /*
     * 驼峰转下划线规则
     * @param string $node
     * @return string
     */
    public static function parseNodeStr($node)
    {
        $tmp = [];
        foreach (explode('/', $node) as $name) {
            $tmp[] = strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
        }
        return trim(join('/', $tmp), '/');
    }

    /*
     * 获取所有PHP文件
     * @param string $dirPath 目录
     * @param array $data 额外数据
     * @param string $ext 有文件后缀
     * @return array
     */
    private static function scan($dirPath, $ext = 'php')
    {
        $dirs = scandir($dirPath);
        $data = [];
        foreach ($dirs as $dir) {
            if (strpos($dir, '.') === 0) {
                continue;
            }
            $tmpPath = realpath($dirPath . DIRECTORY_SEPARATOR . $dir);
            if (is_dir($tmpPath)) {
                $data = array_merge($data, self::scan($tmpPath));
            } elseif (pathinfo($tmpPath, 4) === $ext) {
                $data[] = $tmpPath;
            }
        }
        return $data;
    }

}
