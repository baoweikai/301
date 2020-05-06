<?php
namespace app\admin\controller;

use think\facade\Json;
use think\facade\Env;

class Template extends \app\admin\Controller
{
    protected $filepath,$publicpath,$viewSuffix;
    function initialize()
    {
        parent::initialize();
        $viewPath = config('template.view_path');
        $this->viewSuffix = config('template.view_suffix');
		$viewPath = $viewPath ? $viewPath : 'view';
        $this->filepath =  Env::get('app_path').config('default_module').'/'.$viewPath.'/';
        $this->publicpath =  'static/Home/';
		$this->result['viewSuffix'] = $this->viewSuffix;
    }

    public function index(){
        $type=  input('param.type') ? input('param.type') : $this->viewSuffix;
        if($type==$this->viewSuffix){
            $path=$this->filepath;
        }else{
            $path=$this->publicpath.$type.'/';
        }
        $files = dir_list($path,$type);
        $templates = array();
        
        foreach ($files as $key=>$file){
            $filename = basename($file);
            $templates[$key]['value'] =  substr($filename,0,strrpos($filename, '.'));
            $templates[$key]['filename'] = $filename;
            $templates[$key]['filepath'] = $file;
            $templates[$key]['filesize']= $file? format_bytes(filesize($file)):0;
            $templates[$key]['filemtime']=filemtime($file);
            $templates[$key]['ext'] = strtolower(substr($filename,strrpos($filename, '.')-strlen($filename)));
        }

        $this->result['templates'] = $templates;

        return $this->fetch();
    }


    public function add(){
        if (request()->isPost()) {
            $filename = input('post.file');
            $type = input('post.type');
            $path = $type==$this->viewSuffix ?  $this->filepath : $this->publicpath.$type.'/';
            $file = $path.$filename.'.'.$type;
            if(file_exists($file)){
                Json::fail("文件不存在!");
            }
            file_put_contents($file,stripslashes(input('post.content')));
            if($type==$this->viewSuffix){
                $result['url'] = url('/Template/index');
            }else{
                $result['url'] = url('/Template/index',array('type'=>$type));
            }
            Json::success("添加成功",$result);
        }else{
            $this->result['title'] = '添加模版';
            return $this->fetch();
        }
    }

    
    public function edit(){
        if (request()->isPost()) {
            $filename = input('post.file');
            $type=  input('param.type') ? input('param.type') : $this->viewSuffix;
            $path = $type==$this->viewSuffix ?  $this->filepath : $this->publicpath.$type.'/';
            $file = $path.$filename;
            if(file_exists($file)){
                file_put_contents($file,stripslashes(input('content')));
                if($type==$this->viewSuffix){
                    $result['url'] = url('/Template/index');
                }else{
                    $result['url'] = url('/Template/index',array('type'=>$type));
                }
                Json::success("修改成功",$result);
            }else{
                Json::fail("文件不存在!");
            }
        }else{
            $filename = input('param.file');
            if(input('param.type')){
                $type = input('param.type');
            }else{
                $type = strtolower(substr($filename,strrpos($filename, '.')-strlen($filename)+1));
            }
            $path = $type==$this->viewSuffix ?  $this->filepath : $this->publicpath.$type.'/';
            $file = $path.$filename;
            if(file_exists($file)){
                $file=iconv('gb2312','utf-8',$file);
                $content = file_get_contents($file);
                $this->result['filename'] = $filename;
                $this->result['title'] = '修改模版内容';
                $this->result['file'] = $file;
                $this->result['content'] = $content;
            }else{
                $this->error('文件不存在！');
            }
            return $this->fetch();
         }
    }
   
    public function delete(){
        $filename = input('param.file');
        $type = strtolower(substr($filename,strrpos($filename, '.')-strlen($filename)+1));
        $path = $type==$this->viewSuffix ? $path=$this->filepath : $this->publicpath.$type.'/';
        $file = $path.$filename;
        if(file_exists($file)){
            unlink($file);
            if($type==$this->viewSuffix){
                return redirect('/Template/index');
            }else{
                return redirect('/Template/index',array('type'=>$type));
            }
        }else{
            if($type==$this->viewSuffix){
                return redirect('/Template/index');
            }else{
                return redirect('/Template/index',array('type'=>$type));
            }
        }
    }

    public function images(){
        $path = $this->publicpath.'images/'.input('folder').'/';
        $uppath = explode('/',input('folder'));
        $leve = count($uppath)-1;
        unset($uppath[$leve]);
        if($leve>1){
            unset($uppath[$leve-1]);
            $uppath = implode('/',$uppath).'/';
        }else{
            $uppath = '';
        }
        $this->result['leve'] = $leve;
        $this->result['uppath'] = $uppaths;
        $files = glob($path.'*');
        $folders=array();
        foreach($files as $key => $file) {
            $filename = basename($file);
            if(is_dir($file)){
                $folders[$key]['filename'] = $filename;
                $folders[$key]['filepath'] = $file;
                $folders[$key]['ext'] = 'folder';
            }else{
                $templates[$key]['filename'] = $filename;
                $templates[$key]['filepath'] = $file;
                $templates[$key]['ext'] = strtolower(substr($filename,strrpos($filename, '.')-strlen($filename)+1));
                if(!in_array($templates[$key]['ext'],array('gif','jpg','png','bmp'))) $templates[$key]['ico'] =1;
            }
        }
        $this->result['title'] = '媒体文件';
        $this->result['path'] = $path;
        $this->result['folders'] = $folders;
        $this->result['files'] = $templates;
        return $this->fetch();
    }
    public function imgDel(){
        $path = $this->publicpath.'images/'.input('post.folder');
        $file=$path.input('post.filename');
        if(file_exists($file)){
            is_dir($file) ? dir_delete($file) : unlink($file);
            Json::success("删除成功!");

        }else{
            Json::fail("文件不存在!");
        }
    }
}


?>