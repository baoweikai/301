<?php


namespace helper;

/*
 * 系统权限节点读取器
 * Class NodeService
 * @package extend
 * @date 2017/05/08 11:28
 */
class Node
{
    static $rows;
    /*
     * 应用用户权限节点
     * @return bool
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function apply()
    {
        cache('need_access_node', null);
        $adminid = userid();
        if (!empty($adminid)) {
            $admin = \model\Admin::get($adminid);

            session('admin', $admin->getData());

            $nodes = $admin->auth->nodes;

            return session('Admin.nodes', $nodes);
        }
    }

    /*
     * 检查用户节点权限
     * @param string $node 节点
     * @return bool
     */
    public static function check($node)
    {
        list($module, $controller, $action) = explode('/', str_replace(['?', '=', '&'], '/', $node . '///'));
        $currentNode = Folder::parseNodeStr("{$module}/{$controller}") . strtolower("/{$action}");
        if (session('Admin.username') === 'admin' || stripos($node, 'admin/index') === 0) {
            return true;
        }
        if (!in_array($currentNode, \model\index\Node::getAuthNode())) {
            return true;
        }

        return in_array($currentNode, (array)session('Admin.nodes'));
    }

    /*
     * 树形結構
     */
    public static function _table($pnode = '', $level = 0, $pid = 0){
        $data = [];
        foreach(self::$rows as $k => $row){
            if(!self::isson($row['node'], $pnode)) { continue; } // 不是子菜单, 就跳出当次循环
            $children = self::_table($row['node'], $level + 1, $row['id']);
            $data[] = array_merge($row, [
                'pid' => $pid,
                'count' => count($children),
                'level' => $level,
            ]);
            if(count($children) > 0){
                $data = array_merge($data, $children);
            }
            unset(self::$rows[$k]);
        }
        return $data;
    }
    // 是否子節點
    public static function isson($node, $pnode){
        if($pnode == ''){
            $pattern =  '/^[\w_]+$/i';
        } else {
            $pattern =  '/^' . str_replace('/', '\/', $pnode) . '\/[\w_]+$/i';
        }

        if(preg_match($pattern, $node) == 1){
            return true;
        }
        return false;
    }
    /*
     * 树形结构
     */
    public static function _tree($pnode = ''){
        $data = [];
        foreach(self::$rows as $i => $row){
            if(!self::isson($row['node'], $pnode)) { continue; } // 不是子菜单, 就跳出当次循环
            $children = self::_tree($row['node']);
            $arr = [];
            foreach(self::$attrs as $key => $attr){
                $arr[$key] = $row[$attr];
            }
            $data[] = array_merge($arr, count($children) > 0 ? ['children' => $children] : []);
            unset(self::$rows[$i]);
        }
        return $data;
    }
}
