<?php
namespace app\admin\controller;

use think\Db;

class Img extends Base{
    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 处理商品图片
     */

    public function SaveImg(){
        if ((request()->file('file')) != NULL) {
            $file = request()->file('file');

            $img['pro_img'] = $this->make($file,973,583);
            $img['suolvetu'] = $this->make($file,973,583);
            $img['longimg'] = $this->make($file,973,583);
        }
        $j = [
            'code'=>200,
            'show'=>$img['pro_img'],
            'save'=>$img['pro_img'].','.$img['suolvetu'].','.$img['longimg']
        ];
        return json($j);
    }


    public function make($file,$width,$height){
        $image = \think\Image::open($file);
        $path = date('Ymd') .'1'. '/' . md5(time() . rand(0, 999999)) . '.jpg';
        $savePath = ROOT_PATH . 'public/uploads/' . $path;
        if(!is_dir(ROOT_PATH . 'public/uploads/' .date('Ymd').'1' )){
            mkdir(ROOT_PATH . 'public/uploads/' .date('Ymd').'1',0777,true);
        }
        $image->thumb($width,$height)->save($savePath);
        return '/uploads/' . $path;
    }
}
