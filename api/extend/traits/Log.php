<?php
namespace traits;


/*
 * 操作日志服务
 * Class LogService
 * @package service
 * @date 2017/03/24 13:25
 */
trait Log
{
    /*
     * 写入操作日志
     * @param string $action
     * @param string $content
     * @return bool
     */
    public static function write($action = '行为', $content = "内容描述")
    {
        $request = request();
        $node = strtolower(join('/', [$request->module(), $request->controller(), $request->action()]));
        $data = [
            'ip'       => $request->ip(),
            'node'     => $node,
            'action'   => $action,
            'content'  => $content,
            'username' => session('user.username') . '',
        ];
        return \model\avm\Log::create($data) !== false;
    }

}
