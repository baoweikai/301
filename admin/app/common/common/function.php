<?php
/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

//缓存
function savecache($name = '',$id='') {
	if($name=='Field'){
			if($id){
					$Model = db($name);
					$data = $Model->order('sort')->where('moduleid='.$id)->column('*', 'field');
					$name=$id.'_'.$name;
					$data = $data ? $data : null;
					cache($name, $data);
			}else{
					$module = cache('Module');
					foreach ( $module as $key => $val ) {
							savecache($name,$key);
					}
			}
	}elseif($name=='System'){
			$Model = db ( $name );
			$list = $Model->where(array('id'=>1))->find();
			cache($name, $list);
	}elseif($name=='Module'){
			$Model = db ( $name );
			$list = $Model->order('sort')->select ();
			$pkid = $Model->getPk ();
			$data = array ();
			$smalldata= array();
			foreach ( $list as $key => $val ) {
					$data [$val [$pkid]] = $val;
					$smalldata[$val['name']] =  $val [$pkid];
			}
			cache($name, $data);
			cache('Mod', $smalldata);
	}elseif($name == 'cm'){
			$list = db('category')
					->alias('c')
					->join('module m','c.moduleid = m.id')
					->order('c.sort')
					->field('c.*,m.title as mtitle,m.name as mname')
					->select();
			cache($name, $list);
	}else{
			$Model = db ($name);
			$list = $Model->order('sort')->select ();
			$pkid = $Model->getPk ();
			$data = array ();
			foreach ( $list as $key => $val ) {
					$data [$val [$pkid]] = $val;
			}
			cache($name, $data);
	}
	return true;
}


 /**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login() {
	$user = cache('user_auth_' . session('admin'));
	if (empty($user)) {
		return 0;
	} else {
		return cache('user_auth_sign_' . session('admin')) == data_auth_sign($user) ? $user['id'] : 0;
	}
}

 /**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_user_login() {
	$user = cache('user_auth_' . session('UserAdmin'));
	if (empty($user)) {
		return 0;
	} else {
		return cache('user_auth_sign_' . session('UserAdmin')) == data_auth_sign($user) ? $user['id'] : 0;
	}
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author 
 */
function data_auth_sign($data) {
	//数据类型检测
	if (!is_array($data)) {
		$data = (array) $data;
    }
    ksort($data);//排序
    //url编码并生成query字符串
    $code = http_build_query($data); 
	return  sha1($code);//生成签名
}

function template_file($module=''){
    $viewPath = config('template.view_path');
    $viewSuffix = config('template.view_suffix');
    $viewPath = $viewPath ? $viewPath : 'view';
	$filepath = think\facade\Env::get('app_path').strtolower(config('default_module')).'/'.$viewPath.'/';

    $tempfiles = dir_list($filepath,$viewSuffix);
    $arr=[];
    foreach ($tempfiles as $key=>$file){
        $dirname = basename($file);
        if($module){
            if(strstr($dirname,$module.'_')) {
                $arr[$key]['value'] =  substr($dirname,0,strrpos($dirname, '.'));
                $arr[$key]['filename'] = $dirname;
                $arr[$key]['filepath'] = $file;
            }
        }else{
            $arr[$key]['value'] = substr($dirname,0,strrpos($dirname, '.'));
            $arr[$key]['filename'] = $dirname;
            $arr[$key]['filepath'] = $file;
        }
    }
    return  $arr;
}


function dir_list($path, $exts = '', $list= array()) {
    $path = dir_path($path);
    $files = glob($path.'*');
    foreach($files as $v) {
        $fileext = fileext($v);
        if (!$exts || preg_match("/\.($exts)/i", $v)) {
            $list[] = $v;
            if (is_dir($v)) {
                $list = dir_list($v, $exts, $list);
            }
        }
    }
    return $list;
}
function dir_path($path) {
    $path = str_replace('\\', '/', $path);
    if(substr($path, -1) != '/') $path = $path.'/';
    return $path;
}
function fileext($filename) {
    return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}
function checkField($table,$value,$field){
    $count = db($table)->where(array($field=>$value))->count();
    if($count>0){
        return true;
    }else{
        return false;
    }
}

/**
 * PHP格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '')
{
	$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
	return round($size, 2) . $delimiter . $units[$i];
}

//文件单位换算
function byte_format($input, $dec=0){
    $prefix_arr = array("B", "KB", "MB", "GB", "TB");
    $value = round($input, $dec);
    $i=0;
    while ($value>1024) {
        $value /= 1024;
        $i++;
    }
    $return_str = round($value, $dec).$prefix_arr[$i];
    return $return_str;
}

//时间日期转换
function toDate($time, $format = 'Y-m-d H:i:s') {
    if (empty ( $time )) {
        return '';
    }
    $format = str_replace ( '#', ':', $format );
    return date($format, $time );
}

/**
 * 判断当前访问的用户是  PC端  还是 手机端  返回true 为手机端  false 为PC 端
 *  是否移动端访问访问
 * @return boolean
 */
function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        return true;

    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA']))
    {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT']))
    {
        $clientkeywords = array ('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            return true;
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT']))
    {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
        {
            return true;
        }
    }
    return false;
}



/*
* 获取用户真实IP地址
*/
function get_ip(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $cip = $_SERVER['HTTP_CLIENT_IP'];
    }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }else if(!empty($_SERVER["REMOTE_ADDR"])){
        $cip = $_SERVER["REMOTE_ADDR"];
    }else{
        $cip = '';
    }
    preg_match("/[\d\.]{7,15}/", $cip, $cips);
    $cip = isset($cips[0]) ? $cips[0] : '0';
    unset($cips);
    return $cip;
}
// function get_ip(){
//     if(!empty($_SERVER['HTTP_CLIENT_IP'])){
//         $cip = $_SERVER['HTTP_CLIENT_IP'];
//     }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
//         $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
//     }else if(!empty($_SERVER["REMOTE_ADDR"])){
//         $cip = $_SERVER["REMOTE_ADDR"];
//     }else{
//         $cip = '';
//     }
//     preg_match("/[\d\.]{7,15}/", $cip, $cips);
//     $cip = isset($cips[0]) ? $cips[0] : 'unknown';
//     unset($cips);
//     return $cip;
// }

function get_client_ip_from_ns($proxy_override = true)
{
    if ($proxy_override) {
        /* 优先从代理那获取地址或者 HTTP_CLIENT_IP 没有值 */
        $ip = empty($_SERVER["HTTP_X_FORWARDED_FOR"]) ? (empty($_SERVER["HTTP_CLIENT_IP"]) ? NULL : $_SERVER["HTTP_CLIENT_IP"]) : $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else {
        /* 取 HTTP_CLIENT_IP, 虽然这个值可以被伪造, 但被伪造之后 NS 会把客户端真实的 IP 附加在后面 */
        $ip = empty($_SERVER["HTTP_CLIENT_IP"]) ? NULL : $_SERVER["HTTP_CLIENT_IP"];
    }

    if (empty($ip)) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    /* 真实的IP在以逗号分隔的最后一个, 当然如果没用代理, 没伪造IP, 就没有逗号分离的IP */
    if ($p = strrpos($ip, ",")) {
        $ip = substr($ip, $p+1);
    }

    return trim($ip);
}


function getClientIp($type = 0) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($_SERVER['HTTP_X_REAL_IP']){//nginx 代理模式下，获取客户端真实IP
        $ip=$_SERVER['HTTP_X_REAL_IP'];     
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的ip
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户计算机的ip地址
    }else{
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}


function style($title_style){
	$title_style = explode(';',$title_style);
	return  $title_style[0].';'.$title_style[1];
}


function getvalidate($info){
	$validate_data=array();
	if($info['minlength']) $validate_data['minlength'] = ' minlength:'.$info['minlength'];
	if($info['maxlength']) $validate_data['maxlength'] = ' maxlength:'.$info['maxlength'];
	if($info['required']) $validate_data['required'] = ' required:true';
	if($info['pattern']) $validate_data['pattern'] = ' '.$info['pattern'].':true';
	$errormsg='';
	if($info['errormsg']){
			$errormsg = ' title="'.$info['errormsg'].'"';
	}
	$validate= implode(',',$validate_data);
	$validate= 'validate="'.$validate.'" ';
	$parseStr = $validate.$errormsg;
	return $parseStr;
}
function string2array($info) {
	if($info == '') return array();
	eval("\$r = $info;");
	return $r;
}
function array2string($info) {
	if($info == '') return '';
	if(!is_array($info)){
			$string = stripslashes($info);
	}
	foreach($info as $key => $val){
			$string[$key] = stripslashes($val);
	}
	$setup = var_export($string, TRUE);
	return $setup;
}
//初始表单
function getform($form,$info,$value=''){
	$type = $info['type'];
	return  $form->$type($info,$value);
}


//判断图片的类型从而设置图片路径
function imgUrl($img,$defaul=''){
    if($img){
        if(substr($img,0,4)=='http'){
            $imgUrl = $img;
        }else{
            $imgUrl = $img;
        }
    }else{
        if($defaul){
            $imgUrl = $defaul;
        }else{
            $imgUrl = '/static/admin/images/tong.png';
        }

    }
    return $imgUrl;
}


//密码加密方式
function emcryPwd($pwd = '')
{
    return md5(sha1(md5($pwd)));
}

//跳转IP统计
function  jumpCount($jump_id = 0,$jump_url = '')
{
   $model = Db::name("JumpCount");
   $jump_id = (int)$jump_id;
   $jump_url = trim($jump_url);
   $time = time();
   $dt=date('Y-m-d',$time);
   $new_time = date("Y-m-d",strtotime("$dt+1day"));
    $map[] = [
        0=>  ["j_id",'eq',$jump_id],
        1=>  ["jump_url",'eq',$jump_url],
        2=>  ["get_ip",'eq',get_ip()],
        3=>  ["create_time",'lt',strtotime($new_time)],
    ];

   if($model->where($map)->count("id") == 0)
   {
       $data["j_id"] =  (int)$jump_id;
       $data["jump_url"] =  trim($jump_url);
       $data["get_ip"] = get_ip();
        $model->create($data);
   }else{
        $model->where($map)->setInc("pv",1);
   }

}
//引量ip
function citedCount($jump_id = 0,$jump_url = '')
{
    $model = Db::name("CitedCount");
    $jump_id = (int)$jump_id;
    $jump_url = trim($jump_url);
    $time = time();
    $dt=date('Y-m-d',$time);
    $new_time = date("Y-m-d",strtotime("$dt+1day"));
     $map[] = [
         0=>  ["get_ip",'eq',get_ip()],
         1=>  ["create_time",'lt',strtotime($new_time)],
     ];
    if($model->where($map)->count("id") == 0)
    {
        $data["j_id"] =  (int)$jump_id;
        $data["jump_url"] =  trim($jump_url);
        $data["get_ip"] = get_ip();
        $model->create($data);
        return true;
    }else{
        return false;
    }

}

    //时间验证
    function timeVerify($timeSlot=[])
    {
        $time_slot_hour = config("time_slot_hour");

        $time = time();
        $time_bool = false;
        foreach($timeSlot  as $val){
            $slot = $time_slot_hour[$val]; 
            $Hour = explode("-",$slot);
            $start_time = $Hour[0];
            $endtime = $Hour[1];
            $end_time = $val == 23 ? date("Y-m-d",strtotime("+1 day")) : $endtime;
            if(strtotime($start_time)< $time && $time < strtotime($end_time)) {
                $time_bool = true;
            }
        }
        return $time_bool;
    }
    //ip验证
    function ipVerify($end_ip ="")
    {
        $end_ip = explode(",",$end_ip);
        $getEndIpNumber = substr(get_ip(),-1);
        return in_array($getEndIpNumber,$end_ip);//查找尾数是否存在，存在true 
    }

    /**
     * 参数后缀验证
     * @param $param  参数
     * @return  bool
     */
    function  verifyExt($param ='')
    {
        $ext = substr(strrchr($param,"."),1);
        $extArr = ["js","css","jpg","jpeg","png","gif","xml","rar","zip","exe","pdf","xls","txt","doc","ico"];
        if(array_search($ext,$extArr,false)){
            return true;
        }else{
            return false;
        }


    }
    
?>