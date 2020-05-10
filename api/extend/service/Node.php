<?php


namespace service;

use helper\Folder;

/*
 * 系统权限节点读取器
 * Class NodeService
 * @package extend
 * @date 2017/05/08 11:28
 */
class Node
{
    /*
     * 应用用户权限节点
     * @return bool
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function applyNode()
    {
        cache('need_access_node', null);
        $adminid = session('UserID');
        if (!empty($adminid)) {
            $admin = \app\model\Admin::get($adminid);

            session('admin', $admin->getData());

            $perms = $admin->role->perms;

            return session('Admin.perms', $perms);
        }
    }

    /*
     * 检查用户节点权限
     * @param string $node 节点
     * @return bool
     */
    public static function checkNode($node)
    {
        list($module, $controller, $action) = explode('/', str_replace(['?', '=', '&'], '/', $node . '///'));
        $currentNode = Folder::parseNodeStr("{$module}/{$controller}") . strtolower("/{$action}");
        if (session('Admin.username') === 'admin' || stripos($node, 'admin/index') === 0) {
            return true;
        }
        if (!in_array($currentNode, \app\model\Node::getAuthNode())) {
            return true;
        }

        return in_array($currentNode, (array)session('Admin.nodes'));
    }

    /*
     * 获取系统代码节点
     * @param array $nodes
     * @return array
     */
    public static function format($nodes = [])
    {
        $alias = \app\model\Node::where([])->column('node,is_menu,is_auth,is_login,title');
        $nodes = [];
        $ignore = ['index', 'wechat/review', 'admin/plugs', 'identity/login', 'admin/index'];
        // var_dump(app_path());die();
        foreach (Folder::tree(app_path() . '/') as $thr) {
            foreach ($ignore as $str) {
                if (stripos($thr, $str) === 0) {
                    continue 2;
                }
            }
            $tmp = explode('/', $thr);
            $node = isset($alias[$thr]) ? $alias[$thr] : ['permId' => $thr, 'permName' => '', 'is_menu' => 0, 'is_auth' => 0, 'is_login' => 0];
            switch(count($tmp)){
                case 1:
                    $node['pnode'] = '';
                    break;
                case 2:
                    $node['pnode'] = $tmp[0];
                    break;
                case 3:
                    $node['pnode'] = "{$tmp[0]}/{$tmp[1]}";
                    break;
            }
            $nodes[] = $node;
        }
        /*
        foreach ($nodes as &$node) {
            list($node['is_auth'], $node['is_menu'], $node['is_login']) = [intval($node['is_auth']), intval($node['is_menu']), empty($node['is_auth']) ? intval($node['is_login']) : 1];
        } */
        return $nodes;
    }
}
