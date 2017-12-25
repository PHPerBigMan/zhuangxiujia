<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use Intervention\Image\ImageManagerStatic as Image;
define("SITE_URL",'http://zxj.hwy.sunday.so');


function returnStatus($s){
    if($s){
        $code = 200;
        $msg = "操作成功";
    }else{
       $code=  404;
       $msg = "操作失败";
    }

    return json(['code'=>$code,'msg'=>$msg]);
}

function makeImg(){
    $file = request()->file('file');

    $img = Image::make($file);
    $size = $img->filesize();
    $limit = 100000; // 100kb以上压缩
    if ($size > $limit) {
        $rate = $limit / $size * 75;
        $img->encode('jpg', $rate);
    }
    $path = date('Ymd') . '/' . md5(time() . rand(0, 999999)) . '.jpg';
    $savePath = ROOT_PATH . 'public/uploads/' . $path;
    if(!is_dir(ROOT_PATH . 'public/uploads/' .date('Ymd') )){
        mkdir(ROOT_PATH . 'public/uploads/' .date('Ymd'),0777,true);
    }
    $img->save($savePath);
    return '/uploads/' . $path;
}




function makeQrcode($url){
    $saveUrl = './uploads/';
    $returnUrl = '/uploads/';
    $filename = md5(rand(1000,9999).time()).'.png';
    $imgName = $saveUrl.$filename;
    $returnName = $returnUrl.$filename;

    vendor('phpqrcode.phpqrcode');
    $level = 'L';
    $size = 7;
//    dump($imgName);die;
    header('Content-Type: image/png');
    QRcode::png($url,$imgName,$level,$size);
    return $returnName;
}

/**
 * @param $data 装修热门任务
 * @param $design 设计热门任务
 * @return array
 * author hongwenyang
 * method description : 返回首页任务数据
 */
function Task($data,$design){

    if(empty($data)){
        $data = array();
        $msg = '数据为空';
    }else{
        $msg = '获取数据成功';
    }
    $j = [
        'status'=>200,
        'msg'   =>$msg,
        'data'=>$data,
        'design'=>$design
    ];
//    dump($j);die;
    return $j;
}

/**
 * @param $data
 * @return array
 * author hongwenyang
 * method description : 返回所有数据
 */
function return_data($data){
    if(empty($data)){
        $data = array();
        $msg = '数据为空';
    }else{
        $msg = '获取数据成功';
    }
    $j = [
        'status'=>200,
        'msg'   =>$msg,
        'data'=>$data
    ];
    return $j;
}

