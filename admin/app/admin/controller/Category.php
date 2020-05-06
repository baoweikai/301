<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\Request;
use think\facade\Env;
use think\facade\Json;
use com\Tree;
/**
 * 栏目管理
 */
class Category extends \app\admin\Controller
{
    protected $dao, $categorys , $module,$groupId;
    public function initialize()
    {
        parent::initialize();
        foreach ((array)$this->module as $rw){
            if($rw['type']==1 && $rw['status']==1){
				$data['module'][$rw['id']] = $rw;
			}
        }
        $this->module=$data['module'];
        $this->assign($data);
        unset($data);
        $this->dao = db('category');
        $this->groupId = Db::name('admin_user')->where('id',UID)->value('group_id');
    }

    public function index()
    {
        if ($this->categorys) {
            foreach ($this->categorys as $r) {
                if(UID==1){
                    if ($r['module'] == 'page') {
                        $r['str_manage'] = '<a class="orange" href="' . url('/Page/edit', array('id' => $r['id'])) . '" title="修改内容"><i class="icon icon-file-text2"></i></a> | ';
                    } else {
                        $r['str_manage'] = '';
                    }
                    $r['str_manage'] .= '<a class="blue" title="添加子栏目" href="' . url('/Category/add', array('parentid' => $r['id'])) . '"> <i class="icon icon-plus"></i></a> | <a class="green" href="' . url('/Category/edit', array('id' => $r['id'])) . '" title="修改"><i class="icon icon-pencil2"></i></a> | <a class="red" href="javascript:del(\'' . $r['id'] . '\')" title="删除"><i class="icon icon-bin"></i></a> ';

                    $r['modulename'] = $this->module[$r['moduleid']]['title'];

                    $r['dis'] = $r['ismenu'] == 1 ? '<font color="green">显示</font>' : '<font color="red">不显示</font>';
                    $array[] = $r;
                }else{
                    $groupArr = explode(',',$r['readgroup']);
                    if(in_array($this->groupId,$groupArr)){
                        if ($r['module'] == 'page') {
                            $r['str_manage'] = '<a class="orange" href="' . url('/Page/edit', array('id' => $r['id'])) . '" title="修改内容"><i class="icon icon-file-text2"></i></a> | ';
                        } else {
                            $r['str_manage'] = '';
                        }
                        $r['str_manage'] .= '<a class="blue" title="添加子栏目" href="' . url('/Category/add', array('parentid' => $r['id'])) . '"> <i class="icon icon-plus"></i></a> | <a class="green" href="' . url('/Category/edit', array('id' => $r['id'])) . '" title="修改"><i class="icon icon-pencil2"></i></a> | <a class="red" href="javascript:del(\'' . $r['id'] . '\')" title="删除"><i class="icon icon-bin"></i></a> ';

                        $r['modulename'] = $this->module[$r['moduleid']]['title'];

                        $r['dis'] = $r['ismenu'] == 1 ? '<font color="green">显示</font>' : '<font color="red">不显示</font>';
                        $array[] = $r;
                    }
                }

            }

            $str = "<tr><td class='visible-lg visible-md'>\$id</td>";
            $str .= "<td class='text-left'>\$spacer<a href='/\$module/\$action/\$files/\$id.html' class='green' title='查看内容'>\$catname </a>&nbsp;</td>";

            $str .= "<td class='visible-lg visible-md'>\$modulename</td><td class='visible-lg visible-md'>\$dis</td>";
            $str .= "<td><input type='text' size='10' data-id='\$id' value='\$sort' class='layui-input list_order'></td><td>\$str_manage</td></tr>";
            $tree = new Tree($array);
            $tree->icon = array('&nbsp;&nbsp;&nbsp;│  ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            $categorys = $tree->get_tree(0, $str);
            $this->result['categorys'] = $categorys;
        }else{
          $this->result['categorys'] = '<tr style="text-align:center;"><td colspan="6">暂无数据</td></tr>';
        }
        $this->result['title'] = '栏目列表');
        return $this->fetch();
    }


    public function add(){
        if (request()->isPost()) {
            $data = $data = Request::except('file');
            if(!empty($data['readgroup'])){
                $data['readgroup'] = implode(',',$data['readgroup']);
            }else{
                $data['readgroup'] = $this->groupId;
            }
            $data['module'] = $this->module[$data['moduleid']]['name'];
            $data['child'] = isset($data['child'])?1:0;
            $id = db('category')->insertGetId($data);
            if($id) {
                if($data['module']=='page'){
                    $data2['id']=$id;
                    if($data['title']==''){
                        $data2['title'] = $data['catname'];
                        $data2['content'] = '';
                    }
                    $page=db('page');
                    $page->insert($data2);
                }
                $this->repair();
                $this->repair();
                savecache('Category');
                $arr['url']= url('/Category/index');
                Json::success("栏目添加成功!",$arr);
            }else{
                Json::fail("栏目添加失败!");
            }
        }else{
            $parentid =	input('param.parentid');
            //模型列表
            $module = db('module')->where('status',1)->field('id,title,name')->select();
            $this->result['modulelist'] = $module;
            //父级模型ID
            if($parentid){
                $vo['moduleid'] = $this->categorys[$parentid]['moduleid'];
                $this->result['module'] = $vo;
            }
             
            $array = [];
            //栏目选择列表
            foreach($this->categorys as $r) {
                $array[] = $r;
            }
            $str  = "<option value='\$id' \$selected>\$spacer \$catname</option>";
            $tree = new Tree ($array);
            $categorys = $tree->get_tree(0, $str,$parentid);
            $this->result['categorys'] = $categorys;
            //模版
            $templates= template_file();
            $this->result['templates'] = $templates;
            //管理员权限组
            $usergroup=db('admin_group')->select();
            $this->result['rlist'] = $usergroup;
            $this->result['title'] = '添加栏目';
            return $this->fetch();
        }
    }


    public function edit(){
        if (request()->isPost()) {
            $data = $data = Request::except('file');
            $data['module'] = db('module')->where(array('id'=>$data['moduleid']))->value('name');
            if(!empty($data['readgroup'])){
                $data['readgroup'] = implode(',',$_POST['readgroup']);
            }else{
                $data['readgroup']='';
            }
            $data['arrparentid'] = $this->get_arrparentid($data['id']);
            $data['child'] = isset($data['child']) ? '1' : '0';
            if (false !==db('category')->update($data)) {
                if($data['child']==1){
                    $arrchildid = $this->get_arrchildid($data['id']);
                    $data2['ismenu'] = $data['ismenu'];
                    $data2['pagesize'] = $data['pagesize'];
                    $data2['readgroup'] = $data['readgroup'];
                    db('category')->where( ' id in ('.$arrchildid.')')->update($data2);
                }
                $this->repair();
                $this->repair();
                savecache('Category');
                cache('cate', NULL);
                $result['url'] = url("/Category/index");
                Json::success("栏目修改成功!",$result);
            } else {
                Json::fail("栏目修改失败!");
            }
        }else{
            $id = input('id');
            $this->result['module'] = $this->categorys[$id]['moduleid'];
            $module = db('module')->field('id,title,name')->select();
            $this->result['modulelist'] = $module;

            $record = $this->categorys[$id];
            $record['imgUrl'] = imgUrl($record['image']);
            $record['readgroup'] = explode(',',$record['readgroup']);
            $parentid =	intval($record['parentid']);
            $result = $this->categorys;
            foreach($result as $r) {
                if($r['type']==1) continue;
                $r['selected'] = $r['id'] == $parentid ? 'selected' : '';
                $array[] = $r;
            }
            $str  = "<option value='\$id' \$selected>\$spacer \$catname</option>";
            $tree = new Tree ($array);
            $categorys = $tree->get_tree(0, $str,$parentid);
            $this->result['categorys'] = $categorys;
            $this->result['record'] = $record;
            $usergroup=db('admin_group')->select();
            $this->result['rlist'] = $usergroup;
            $this->result['title'] = '编辑栏目';
            //模版
            $templates= template_file();
            $this->result['templates'] = $templates;
            return $this->fetch();
        }
    }

    public function repair() {
        @set_time_limit(500);
        $this->categorys = $categorys = array();
        $categorys =  db('category')->where("parentid=0")->order('sort ASC,id ASC')->select();
        $this->set_categorys($categorys);
        if(is_array($this->categorys)) {
            foreach($this->categorys as $id => $cat) {
                if($id == 0 || $cat['type']==1) continue;
                $this->categorys[$id]['arrparentid'] = $arrparentid = $this->get_arrparentid($id);
                $this->categorys[$id]['arrchildid'] = $arrchildid = $this->get_arrchildid($id);
                $this->categorys[$id]['parentdir'] = $parentdir = $this->get_parentdir($id);
                db('category')->update(array('parentdir'=>$parentdir,'arrparentid'=>$arrparentid,'arrchildid'=>$arrchildid,'id'=>$id));
            }
        }

    }

    public function set_categorys($categorys = array()) {
        if (is_array($categorys) && !empty($categorys)) {
            foreach ($categorys as $id => $c) {
                $this->categorys[$c['id']] = $c;
                $r = db('category')->where(array("parentid"=>$c['id']))->Order('sort ASC,id ASC')->select();
                $this->set_categorys($r);
            }
        }
        return true;
    }


    public function get_arrparentid($id, $arrparentid = '') {
        if(!is_array($this->categorys) || !isset($this->categorys[$id])) return false;
        $parentid = $this->categorys[$id]['parentid'];
        $arrparentid = $arrparentid ? $parentid.','.$arrparentid : $parentid;
        if($parentid) {
            $arrparentid = $this->get_arrparentid($parentid, $arrparentid);
        } else {
            $this->categorys[$id]['arrparentid'] = $arrparentid;
        }
        return $arrparentid;
    }
    public function get_arrchildid($id) {
        $arrchildid = $id;
        if(is_array($this->categorys)) {
            foreach($this->categorys as $catid => $cat) {
                if($cat['parentid'] && $id != $catid) {
                    $arrparentids = explode(',', $cat['arrparentid']);
                    if(in_array($id, $arrparentids)){
                        $arrchildid .= ','.$catid;
                    }
                }
            }
        }
        return $arrchildid;
    }
    public function get_parentdir($id) {
        if($this->categorys[$id]['parentid']==0){
            return '';
        }
        $arrparentid = $this->categorys[$id]['arrparentid'];
        unset($r);
        if ($arrparentid) {
            $arrparentid = explode(',', $arrparentid);
            $arrcatdir = array();
            foreach($arrparentid as $pid) {
                if($pid==0) continue;
                $arrcatdir[] = $this->categorys[$pid]['catdir'];
            }
            return implode('/', $arrcatdir).'/';
        }
    }

    
    public function del() {
        $catid = input('param.id');
        $modules = $this->categorys[$catid]['module'];

        $modulesId = $this->categorys[$catid]['moduleid'];
        $scount = $this->dao->where(array('parentid'=>$catid))->count();

        if($scount){
            Json::fail("请先删除其子栏目!");
        }

        $module  = db($modules);
        $arrchildid = $this->categorys[$catid]['arrchildid'];

        if($modules != 'page'){
            $fields = cache($modulesId.'_Field');
            $fieldse=array();
            foreach ($fields as $k=>$v){
                $fieldse[] = $k;
            }
            if(in_array('catid',$fieldse)){
                $count = $module->where('catid','in',$arrchildid)->count();
            }else{
                $count = $module->count();
            }
            if($count){
                Json::fail("请先删除该栏目下所有数据!");
            }
        }
        $pid = $this->categorys[$catid]['parentid'];

        $scat = $this->dao->where(array('parentid'=>$pid))->count();
        if($scat==1){
            db('category')->where(array('id'=>$pid))->update(array('child'=>0));
        }
        db('category')->where('id','in',$arrchildid)->delete();
        $arr=explode(',',$arrchildid);
        foreach((array)$arr as $r){
            if($this->categorys[$r]['module']=='page'){
                $module=db('page');
                $module->delete($r);
            }
        }
        $this->repair();
        savecache('Category');
        cache('cate', NULL);
        $result['url'] = url('/Category/index');
        Json::success("栏目删除成功!",$result);

    }

    public function cOrder(){
        $data = input('post.');
        $this->dao->update($data);
        savecache('Category');
        cache('cate', NULL);
        $result['url'] = url('/Category/index');
        Json::success("排序成功!",$result);
    }
}

?>