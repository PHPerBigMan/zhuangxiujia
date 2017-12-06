<?php
namespace app\admin\controller;

use app\common\model\BookLose;
use think\Db;
use think\View;

class Tixian extends Base
{
    public function index()
    {
//        echo time();die;
        $count = Db::name('Tixian')->count();
        $j = [
            'title'     =>"提现管理",
            'count'=>$count
        ];
        return view("tixian/index",$j);
    }

    public function tixian_ajax(){
        $data = Db::name('Tixian')->select();
        foreach($data as $k=>$v){
            $font = "style=\"font-size:14px\"";
            $data[$k]['user_name']      = Db::name('User')->where(['id'=>$v['user_id']])->value('user_name');
            $data[$k]['create_time']    = date('Y-m-d H:i:s',$v['create_time']);
            $data[$k]['end_time']       = date('Y-m-d H:i:s',$v['end_time']);


            switch($v['status']){
                case 0:
                    $msg = "<span class=\"label label-defaunt radius\" $font>提现中</span>";
                    break;
                case 1:
                    $msg = "<span class=\"label label-success radius\" $font>提现成功</span>";
                    break;
                default:
                    $msg = "<span class=\"label label-defaunt radius\" $font>提现失败</span>";
            }

            $data[$k]['status'] = $msg;

            switch($v['shenghe']){
                case 0:
                    $sh = "<span class=\"label label-defaunt radius\" $font>不通过</span>";
                    break;
                case 1:
                    $sh = "<span class=\"label label-success radius\" $font>通过</span>";
                    break;
            }

            $data[$k]['shenghe'] = $sh;

            $id = $v['id'];

            $money = $v['money'];
            $data[$k]['caozuo'] = "<a style=\"text-decoration:none\" onclick=\"shenghe(this,$id)\">审核</a> | <a onclick='dakuan(this,$id,$money)'>打款</a>";

        }

        return json(['data'=>$data]);
    }


    /**
     * @return \think\response\Json
     * 审核
     */

    public function shenghe(){
        $id = input('id');

        $shenghe  = Db::name('Tixian')->where(['id'=>$id])->value('shenghe') == 0 ? 1 : 0;

        $s = Db::name('Tixian')->where(['id'=>$id])->update(['shenghe'=>$shenghe]);
        if($s){
            $code = 200;
        }else{
            $code = 404;
        }

        return json($code);
    }

    /**
     * 打款
     */

    public function dakuan(){
        $id = input('id');
        //打款
        $money = input('money');

        $status = Db::name('Tixian')->where(['id'=>$id])->value('status');
        if($status == 1){
            //用户已提现成功
            $code = 100;
        }else{
            //打款成功后减去对应用户的账户余额
            Db::name("User")->where(['id'=>Db::name('Tixian')->where(['id'=>$id])->value('user_id')])->setDec('user_money',$money);
        }
    }



}
