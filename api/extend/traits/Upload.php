<?php
namespace common\behaviors;

use Yii;
use yii\base\Behavior;
use yii\web\UploadedFile;
use common\components\Uploader;
use common\components\Base64Uploader;
/*
 * 层级结构类.
 *
 * @author Weikai
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

class FileBehavior extends Behavior
{
    protected $savePath;
    protected $showPath;
    protected $uploadAttr;
    public $uploadUrl;

    /*
     * 文件上传
     */
    public function upload($f){
        $uploader = new Uploader;
        $this->owner->scenario = 'upload';
        foreach($f as $a => $val){
            if(empty($val)){
                return false;
            }
            $this->owner->$a='';
            $uploader->fullName = '';
            if(is_array($val)&&!$this->uploadMultipleFile($uploader, $a)){
                return false;
            }elseif(!$this->uploadSingleFile($uploader, $a)){
                return false;
            }
        }
        return true;
    }

    /*
     * base64文件上传
     */
    public function base64Upload($attributes){
        $uploader = new Base64Uploader;
        $this->owner->scenario='upload';
        // 循环各个atribute
        foreach($attributes as $attr => $val){
            if(empty($val)){
                return false;
            }

            $this->owner->$attr='';
            $uploader->fullName='';
            if(is_array($val)&&!$uploader->uploadMultipleFile($this->owner, $attr, $val)){
                return false;
            }elseif(!$uploader->uploadSingleFile($this->owner, $attr, $val)){
                $this->owner->uploadUrl=$uploader->fullName;
                return false;
            }
            $this->owner->uploadUrl=$uploader->fullName;
        }
        return true;
    }

    /*
     * 多个文件
     */
    private function uploadMultipleFile($uploader, $attr){
        $files=UploadedFile::getInstances($this->owner, $attr);
        foreach($files as $file){
            $this->owner->setAttribute($attr, $file);

            if (!$this->owner->validate($attr)) {
                $this->stateInfo=$this->owner->errorString;
                return false;
            }
            $this->owner->setAttribute($attr, $file);

            if (!$this->owner->validate()) {
                //$this->stateInfo=$model->errorString;
                return false;
            }
            if($uploader->upload($file)){
                $this->uploadUrl=$this->owner->$attr.=','.$uploader->fullName;
            }else{
                $this->owner->addError($attr, $uploader->stateInfo);
                return false;
            }
        }
        $this->owner->$attr=trim($this->owner->$attr, ',');
        return true;
    }

    /*
     * 单个文件
     */
    private function uploadSingleFile($uploader, $attr){
        $file=UploadedFile::getInstance($this->owner, $attr);
        if($uploader->upload($file)){
            $this->uploadUrl = $this->owner->$attr = $uploader->fullName;
            return true;
        }else{
            return false;
        }
    }

	/*
	 * 移动文件
	 */
	public function moveFile()
	{
        $oldFolder=Yii::$app->params['uploadPath'].Yii::$app->request->csrfToken;
        $newFolder=Yii::$app->params['uploadPath'].self::getName()."/{$this->id}";
        // 删除源文件夹
        rename($oldFolder, $newFolder);
        if(is_file($oldFolder)){
          @unlink($oldFolder);
        }
	}

    /*
     * 获取上传后文件路径
     */
    public function getUploadUrl(){
        return $this->uploadUrl;
    }

    /*
     * 删除
     */
    public function delete($file){
        if(is_file($file)){
          unlink($file);
        }
    }
}
