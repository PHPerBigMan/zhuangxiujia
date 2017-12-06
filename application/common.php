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
    if($file){
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $filename = '/uploads/'.$info->getSaveName();
        }
    }
    return $filename;
}
