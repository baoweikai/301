<?php
namespace app\admin\controller;

use app\admin\Controller;
use think\Image;

class Upload extends Controller
{
    protected $middleware = ['auth'];
    public function uploadImg()
    {
        $type = trim(input('type'));
        if($type){
            $move = 'uploads/'.$type;
        }else{
            $move = 'uploads';
        }

        // 获取上传文件表单字段名
        $fileKey = array_keys(request()->file());
        // 获取表单上传文件
        $file = request()->file($fileKey['0']);
        $info = $file->validate(['ext' => 'jpg,png,gif,jpeg'])->move($move);
        if($info){
            $path = str_replace('\\','/',$info->getSaveName());
            $imgpath =  '/'.$move.'/'. $path;
            if($type == 'Goods') {
                $image  = Image::open('.'.$imgpath);
                $date_path = $move.'/thumb/'.date('Ymd');
                if(!file_exists($date_path)){
                    mkdir($date_path,0777,true);
                }
                $thumb_path = $date_path.'/'.$info->getFilename();
                $image->thumb(150, 150,Image::THUMB_FIXED)->save($thumb_path);
                $result['thumb_url'] = '/'.$thumb_path;
            }
            $result['url'] = $imgpath;
            return $this->success('上传成功',$result);
        }else{
            return $this->error($file->error);
        }
    }
}

?>