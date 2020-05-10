<?php
/***********
** 海报生成**
************/

namespace helper;

class Poster{
    private $canvas;
    private $bgcolor; // 背景色
    private $width = 320; // 画布宽度
    private $height = 600; // 画布高度
    private $basePath;
    private $baseUrl;

    public function __construct()
    {
        $this->init();
    }

    /*
     * 初始化
     */
    public function init(){
        $this->basePath = env('root_path') . 'public/upload/';
        $this->baseUrl = '';
        // 创建画布,并用白色填充
        $this->canvas = imageCreatetruecolor($this->width,$this->height);
        $this->bgcolor = imagecolorallocate($this->canvas, 255, 255, 255);
        imagefill($this->canvas, 0, 0, $this->bgcolor);
    }
    /**
     * 生成宣传海报
     * @param array  参数,包括图片和文字
     * @param string  $filename 生成海报文件名,不传此参数则不生成文件,直接输出图片
     * @return [type] [description]
     */
    function save($filename = ''){
        //生成图片
        if(!empty($filename)){
            $path = $this->basePath . $filename;
            $res = imagejpeg ($this->canvas, $path,90); //保存到本地
            imagedestroy($this->canvas);
            if(!$res) return false;
            return $filename;
        }else{
            if(empty($filename)) header("content-type: image/png");
            imagejpeg ($this->canvas);     //在浏览器上显示
            imagedestroy($this->canvas);
        }
    }
    /*
     * 画图
     */
    public function image($src, $config = []){
        $default = [
            'left' => 0,
            'top' => 0,
            'right' => 0,
            'bottom' => 0,
            'width' => 320,
            'height' => 500,
            'opacity' => 100
        ];
        $params = $config + $default;
        $image = imagecreatefromjpeg($src);
        $info = getimagesize($src);
        //$val['left'] = $val['left'] < 0 ? $this->width- abs($val['left']) - $val['width'] : $val['left'];
        //$val['top'] = $val['top'] < 0 ? $this->height- abs($val['top']) - $val['height'] : $val['top'];
        //放置图像
        imagecopymerge($this->canvas, $image, $params['left'],$params['top'],$params['right'],$params['bottom'],$params['width'],$params['height'],$params['opacity']);//左，上，右，下，宽度，高度，透明度
        imagedestroy($image);
    }
    /*
     * 文字
     */
    public function text($content, $config = []){
        $default = [
            'text' => '',
            'left' => 0,
            'top' => 0,
            'fontSize' => 32,       //字号
            'fontColor' => '255,255,255', //字体颜色
            'angle' => 0,
        ];
        $params = $config + $default;
        list($R, $G, $B) = explode(',', $params['fontColor']);
        $fontColor = imagecolorallocate($this->canvas, $R, $G, $B);
        $params['left'] = $params['left'] < 0 ? $this->width- abs($params['left']) : $params['left'];
        $params['top'] = $params['top'] < 0 ? $this->height- abs($params['top']) : $params['top'];
        imagestring($this->canvas, 5, 0, 0, 'abcdefghijk', $fontColor);
        // imagettftext($this->canvas, $val['fontSize'], $params['angle'], $params['left'], $params['top'], $fontColor, $params['fontPath'], $content);
    }
    /*
     * 头像
     */
    public function avatar($src, $config = []){
        $default = [
            'left' => 0,
            'top' => 0,
            'right' => 0,
            'bottom' => 0,
            'width' => 0,
            'height' => 0,
            'opacity' => 0
        ];
        $params = $config + $default;
        $image = imagecreatefromgif($src);
        $info = getimagesize($src);
        //$val['left'] = $val['left'] < 0 ? $this->width- abs($val['left']) - $val['width'] : $val['left'];
        //$val['top'] = $val['top'] < 0 ? $this->height- abs($val['top']) - $val['height'] : $val['top'];
        //放置图像
        imagecopymerge($this->canvas, $image, $params['left'],$params['top'],$params['right'],$params['bottom'],$params['width'],$params['height'],$params['opacity']);//左，上，右，下，宽度，高度，透明度
    }
    /*
     * 生成二维码
     */
    public function qrcode($content, $qrfile){
        include_once('Qrcode.php');
        $errorCorrectionLevel = 'L';//容错级别
        $matrixPointSize = 6;//生成图片大小

        //生成二维码图片
        QRcode::png($content, $qrfile, $errorCorrectionLevel, $matrixPointSize, 2);
    }
}
