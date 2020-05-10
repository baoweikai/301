<?php


namespace helper;

/*
 * 导入导出
 * Class ToolsService
 * @package service
 * @date 2016/10/25 14:49
 */
class Export
{

    /*
     * Cors Options 授权处理
     */
    public static function corsOptionsHandler()
    {
        if (request()->isOptions()) {
            header('Access-Control-Allow-Origin:*');
            header('Access-Control-Allow-Credentials:true');
            header('Access-Control-Allow-Methods:GET,POST,OPTIONS');
            header('Access-Control-Allow-Headers:Accept,Referer,Host,Keep-Alive,User-Agent,X-Requested-With,Cache-Control,Cookie');
            header('Content-Type:text/plain charset=UTF-8');
            header('Access-Control-Max-Age:1728000');
            header('HTTP/1.0 204 No Content');
            header('Content-Length:0');
            header('status:204');
            exit;
        }
    }

    /*
     * Cors Request Header信息
     * @return array
     */
    public static function corsRequestHander()
    {
        return [
            'Access-Control-Allow-Origin'      => request()->header('origin', '*'),
            'Access-Control-Allow-Methods'     => 'GET,POST,OPTIONS',
            'Access-Control-Allow-Credentials' => "true",
        ];
    }

    /*
     * 写入CSV文件头部
     * @param string $filename 导出文件
     * @param array $headers CSV 头部(一级数组)
     */
    public static function setCsvHeader($filename, array $headers)
    {
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=" . iconv('utf-8', 'gbk//TRANSLIT', $filename));
        echo @iconv('utf-8', 'gbk//TRANSLIT', "\"" . implode('","', $headers) . "\"\n");
    }

    /*
     * 写入CSV文件内容
     * @param array $list 数据列表(二维数组或多维数组)
     * @param array $rules 数据规则(一维数组)
     */
    public static function setCsvBody(array $list, array $rules)
    {
        foreach ($list as $data) {
            $rows = [];
            foreach ($rules as $rule) {
                $item = self::parseKeyDot($data, $rule);
                $rows[] = $item === $data ? '' : $item;
            }
            echo @iconv('utf-8', 'gbk//TRANSLIT', "\"" . implode('","', $rows) . "\"\n");
            flush();
        }
    }

    /*
     * 根据数组key查询(可带点规则)
     * @param array $data 数据
     * @param string $rule 规则，如: order.order_no
     * @return mixed
     */
    private static function parseKeyDot(array $data, $rule)
    {
        list($temp, $attr) = [$data, explode('.', trim($rule, '.'))];
        while ($key = array_shift($attr)) {
            $temp = isset($temp[$key]) ? $temp[$key] : $temp;
        }
        return (is_string($temp) || is_numeric($temp)) ? str_replace('"', '""', "\t{$temp}") : '';
    }

}
