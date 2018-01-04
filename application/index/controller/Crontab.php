<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Crontab extends  Controller
{
    public function CheckBidTime(){
        // 获取所有任务的 投标剩余时间戳
        // TODO::先放一下
        $data = \app\model\Renwu::select();

       if(!empty($data)){
           foreach($data as $v){
               if($v['pay_limit_time']>0){
//                   \app\model\Renwu::where('id',$v['id'])->setDec('pay_limit_time',1);
               }
           }
       }
    }
}
