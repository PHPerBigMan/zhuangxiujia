<?php
namespace app\index\controller;

use think\Db;

class Cover
{
    /**
     * 入口大图、登录小图、轮播图
     */

    public function index()
    {
        $type   = input('type');
        if($type == 2){
            $Img    = Db::name('Banner')->field('banner_url url')->select();
        }else{
            $Img    = Db::name('Img')->where(['type'=>$type])->field('img')->find();
        }

        if(empty($Img)){
            $status = 200;
            $msg    = "数据为空";
        }else{
            $status = 200;
            $msg    = "获取数据成功";
        }
        $j = [
            'data'  =>$Img,
            'status'=>$status,
            'msg'   =>$msg
        ];
        return json($j);

      
    }
}
